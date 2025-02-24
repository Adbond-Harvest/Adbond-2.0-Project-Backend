<?php

namespace app\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use app\Http\Controllers\Controller;

use app\Http\Requests\client\MakeOfferPayment;
use app\Http\Requests\client\PrepareOfferPayment;
use app\Http\Requests\client\InitializeCardPayment;

use app\Http\Resources\PaymentResource;

use app\Services\PaymentService;
use app\Services\OfferBidService;
use app\Services\OfferService;
use app\Services\PackageService;
use app\Services\FileService;

use app\Models\PaymentStatus;
use app\Models\Client;
use app\Models\Offer;
use app\Models\PaymentMode;

use app\Utilities;
use app\Helpers;

use app\Enums\PaymentPurpose;
use app\Enums\FilePurpose;

class OfferPaymentController extends Controller
{
    private $paymentService;
    private $bidService;
    private $offerService;
    private $packageService;
    private $fileService;

    public function __construct()
    {
        $this->paymentService = new PaymentService;
        $this->bidService = new OfferBidService;
        $this->offerService = new OfferService;
        $this->packageService = new PackageService;
        $this->fileService = new FileService;
    }

    public function preparePayment(PrepareOfferPayment $request)
    {
        $bid = $this->bidService->getBid($request->validated("bidId"));
        if(!$bid) return Utilities::error402("Bid was not found");

        if($bid->client_id != Auth::guard('client')->user()->id) return Utilities::error402("You cannot make payment on a bid that its not your own");

        if(!$bid->accepted || $bid->accepted == 0) return Utilities::error402("This bid has not been accepted");

        if($bid->cancelled && $bid->cancelled == 1) return Utilities::error402("This bid has been cancelled");

        if(!$bid->offer) return Utilities::error402("The offer for this bid cannot be found");
        $offer = $bid->offer;

        if(!$offer->approved) return Utilities::error402("This offer has not been approved");

        if($offer->resell_order_id) return Utilities::error402("You cannot make payment on a resell order offer");

        $processingId = Utilities::getOfferProcessingId();
        // Save in Cache
        Cache::put('offer_processing_' . $processingId, ["bidId" => $bid->id, "amount" => $bid->price, "clientId" => Auth::guard('client')->user()->id], now()->addHours(12));

        return Utilities::ok([
            "processingId" => $processingId,
            "amount" => $bid->price
        ]);
    }

    public function initializeCardPayment(InitializeCardPayment $request)
    {
        try{
            $processingId = $request->validated("processingId");
            $processedData = Cache::get('offer_processing_' . $processingId);
            if(!$processedData) return Utilities::error402("processing Id has expired.. Go back and prepare the order again");
            $res = $this->paymentService->paystackInit(Auth::guard('client')->user(), $processedData['amount']*100);
            // dd($res);
            if($res['success']==true) {
                $processedData['reference'] = $res['data']['reference'];
                if(isset($data['processingId'])) Cache::forget('order_processing_' . $processingId);
                return Utilities::ok($res['data']);
            }else{
                return Utilities::error402("failed to initialize payment.. ".$res['message']);
            }
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to perform this operation, Please try again later or contact support');
        }
    }

    public function makePayment(MakeOfferPayment $request)
    {
        try{
            DB::beginTransaction();

            $data = $request->validated();
            $processedData = Cache::get('offer_processing_' . $data['processingId']);
            if(!$processedData) return Utilities::error402("processing Id has expired.. Go back and prepare the offer payment again");

            if(Auth::guard('client')->user()->id != $processedData['clientId']) return Utilities::error402("You are not authorized to make this payment");

            $bid = $this->bidService->getBid($processedData['bidId']);
            if(!$bid) return Utilities::error402("Bid was not found");

            if(!$bid->offer) return Utilities::error402("The offer for this bid cannot be found");
            $offer = $bid->offer;

            // Set up paymentData parameters
            $paymentData = [
                "clientId" => Auth::guard("client")->user()->id,
                "purchaseType" => Offer::$type,
                "purchaseId" => $offer->id,
                "paymentModeId" => ($data['cardPayment']) ? PaymentMode::cardPayment()->id : PaymentMode::bankTransfer()->id,
                "purpose" => PaymentPurpose::OFFER_PAYMENT->value,
                "receiptNumber" => Helpers::generateReceiptNo($offer->id, Auth::guard('client')->user()->id, $data['processingId']),
                "paymentDate" => ($data['cardPayment']) ? now() : $data['paymentDate']
            ];

            $paymentData['amount'] = $bid->price;

            if($data['cardPayment']) {
                $res = $this->paymentService->paystackVerify($data['reference'], $bid->price);
                if($res['success']) {
                    $paymentData['success'] = true;
                    if(!$res['paymentError']) {
                        $paymentData['confirmed'] = true;
                    }else{
                        $paymentData['flag'] = true;
                        $paymentData['flagMessage'] = $res['message'];
                    }
                }else{
                    $paymentData['failureMessage'] = $res['message'];
                    $paymentData['success'] = false;
                }
            }else{
                if(!$request->hasFile('evidence')) return Utilities::error402("Payment Evidence file is missing");
                $data = $this->uploadEvidence($request->file('evidence'), $data);
            }
            $payment = $this->paymentService->save($paymentData);
            if($data['cardPayment'] && isset($paymentData['confirmed'])) {
                $this->paymentService->uploadReceipt($payment, Auth::guard('client')->user());
                $this->offerService->update(['paymentStatusId' => PaymentStatus::complete()->id], $offer);
            }

            DB::commit();

            return Utilities::ok(new PaymentResource($payment));

        }catch(\Exception $e){
            DB::rollBack();
            return Utilities::error($e, 'An error occurred while trying to perform this operation, Please try again later or contact support');
        }
    }

    private function uploadEvidence($evidence, $data)
    {
        $purpose = FilePurpose::PAYMENT_EVIDENCE->value;
        $fileType = (str_starts_with($evidence->getMimeType(), 'image/')) ? 'image' : 'pdf';
        // dd(str_starts_with($request->file('evidence')->getMimeType(), 'image/'));
        // dd($request->file('evidence')->isValid());
        $res = $this->fileService->save($evidence, $fileType, Auth::guard('client')->user()->id, $purpose, CLient::$userType, 'payment-evidences');
        if($res['status'] != 200) return Utilities::error402('Sorry Payment Evidence could not be uploaded '.$res['message']);

        $data['evidenceFileId'] = $res['file']->id;
        $data['paymentStatusId'] = PaymentStatus::pending()->id;

        return $data;
    }
}
