<?php

namespace app\Http\Controllers\Client;

use app\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use app\Http\Requests\Client\SavePayment;
use app\Http\Requests\Client\InitializeCardPayment;

use app\Mail\NewOrder;
use app\Mail\NewPayment;

use app\Services\PackageService;
use app\Services\PromoCodeService;
use app\Services\OrderService;
use app\Services\PaymentService;
use app\Services\FileService;
use app\Services\CommissionService;

use app\Models\PaymentStatus;
use app\Models\PaymentMode;
use app\Models\PaymentGateway;

use app\Http\Resources\OrderResource;

use app\Enums\PaymentPurpose;
use app\Enums\FilePurpose;
use app\Enums\FileTypes;

use app\Utilities;
use app\Helpers;


class PaymentController extends Controller
{
    private $paymentService;
    private $packageService;
    private $promoCodeService;
    private $orderService;
    private $fileService;
    private $commissionService;

    private static $userType = "app\Models\Client";

    public function __construct()
    {
        $this->paymentService = new PaymentService;
        $this->packageService = new PackageService;
        $this->promoCodeService = new PromoCodeService;
        $this->orderService = new OrderService;
        $this->fileService = new FileService;
        $this->commissionService = new CommissionService;
    }

    public function initializeCardPayment(InitializeCardPayment $request)
    {
        try{
            $processingId = $request->validated("processingId");
            $processedData = Cache::get('order_processing_' . $processingId);
            if(!$processedData) return Utilities::error402("processing Id has expired.. Go back and prepare the order again");
            
            $res = $this->paymentService->paystackInit(Auth::guard('client')->user(), $processedData['amountPayable']);
            if($res['success']==true) {
                $processedData['reference'] = $res['data']['reference'];
                if(isset($data['processingId'])) Cache::forget('order_processing_' . $processingId);
                Cache::put('order_processing_' . $processingId, $processedData, now()->addHours(12));
                return Utilities::ok($res['data']);
            }else{
                return Utilities::error402("failed to initialize payment.. ".$res['message']);
            }
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to perform this operation, Please try again later or contact support');
        }
    }

    public function save(SavePayment $request)
    {
        // try{
            DB::beginTransaction();
            $data = $request->validated();
            $processedData = Cache::get('order_processing_' . $data['processingId']);
            if(!$processedData) return Utilities::error402("processing Id has expired.. Go back and prepare the order again");
            $package = $this->packageService->package($processedData['packageId']);
            $order = null;
            if($data['cardPayment']) { // if it is a card payment
                // verify card payment
                $res = $this->paymentService->paystackVerify($data['reference'], $processedData['amountPayable']);

                // set payment Status depending on whether everything checks out in verifying the card payment or not
                $data['paymentStatusId'] =  ($res['success']==true) ? 
                    (($processedData['isInstallment']) ? PaymentStatus::deposit()->id : PaymentStatus::complete()->id) : PaymentStatus::pending()->id;

                if($processedData['isInstallment']) $data['installmentsPayed'] = 1;
                // save the order
                $order = $this->saveOrder($data, $processedData, $package);

                // Save the payment
                $payment = $this->savePayment($order, $data, $processedData, $res);
                // dd('success false');
                // Send email to client about the receipt

            }else{ // if its a bank payment
                // Save Payment evidence
                $purpose = FilePurpose::PAYMENT_EVIDENCE->value;
                $fileType = (str_starts_with($request->file('evidence')->getMimeType(), 'image/')) ? 'image' : 'pdf';
                // dd(str_starts_with($request->file('evidence')->getMimeType(), 'image/'));
                // dd($request->file('evidence')->isValid());
                $res = $this->fileService->save($request->file('evidence'), $fileType, Auth::guard('client')->user()->id, $purpose, self::$userType, 'payment-evidences');
                if($res['status'] != 200) return Utilities::error402('Sorry Payment Evidence could not be uploaded '.$res['message']);

                $data['evidenceFileId'] = $res['file']->id;
                $data['paymentStatusId'] = PaymentStatus::pending()->id;
                $order = $this->saveOrder($data, $processedData, $package);
                $payment = $this->savePayment($order, $data, $processedData);
            }

            DB::commit();
            $order->load("package")->load("discounts")->load("paymentStatus");
        
            return Utilities::ok(new OrderResource($order));
        // }catch(\Exception $e){
        //     DB::rollBack();
        //     return Utilities::error($e, 'An error occurred while trying to perform this operation, Please try again later or contact support');
        // }

    }

    private function saveOrder($data, $processedData, $package)
    {
        $data['clientId'] = Auth::guard('client')->user()->id;
        $data['packageId'] = $processedData['packageId'];
        $data['isInstallment'] = $processedData['isInstallment'];
        $data['units'] = $processedData['units'];
        if($processedData['isInstallment']) $data['installmentCount'] = $processedData['installmentCount'];
        if(isset($processedData['promoCode'])) $data['promoCodeId'] = $this->promoCodeService->promoCode($processedData['promoCode'])->id;
        $data['amountPayable'] = $processedData['amountDetail']['amount'];
        $data['amountPayed'] = $processedData['amountPayable'];
        $data['unitPrice'] = $package->amount;
        $data['balance'] = ($data['isInstallment']) ? ($data['amountPayable'] - $data['amountPayed']) : 0;
        $data['orderDate'] = (isset($data['orderDate'])) ? $data['orderDate'] : now();
        if($data['isInstallment'] && $package->installment_duration) $data['paymentDueDate'] = now()->addMonths($package->installment_duration);

        $order = $this->orderService->save($data);
        $order->order_number = $order->id.$data['processingId'];
        $order->update();

        if($processedData['amountDetail']['appliedDiscounts'] && count($processedData['amountDetail']['appliedDiscounts']) > 0) {
            $this->orderService->saveOrderDiscounts($order, $processedData['amountDetail']['appliedDiscounts']);
        }

        $order->load("client")->load("package")->load("discounts");

        // Send email to client
        try{
            Mail::to($order->client->email)->send(new NewOrder($order));
        }catch(\Exception $e) {
            Utilities::logStuff("Error Occurred while attempting to send Order Email..".$e);
        }

        return $order;
    }

    private function savePayment($order, $data, $processedData, $gatewayRes=null)
    {
        $paymentData['clientId'] = Auth::guard('client')->user()->id;
        $paymentData['purpose'] = ($processedData['isInstallment']) ? PaymentPurpose::INSTALLMENT_PAYMENT->value : PaymentPurpose::PACKAGE_FULL_PAYMENT->value;
        $paymentData['paymentModeId'] = ($data['cardPayment']) ? PaymentMode::cardPayment()->id : PaymentMode::bankTransfer()->id;
        if($data['cardPayment']) {
            if($gatewayRes['success']) {
                $paymentData['success'] = true;
                $paymentData['confirmed'] = true;
            }else{
                $paymentData['confirmed'] = false;
                if($gatewayRes['paymentError']) {
                    $paymentData['flag'] = false;
                    $paymentData['failureMessage'] = $gatewayRes['message'];
                    $paymentData['success'] = false;
                }else{
                    $paymentData['success'] = true;
                    $paymentData['flag'] = true;
                    $paymentData['flagMessage'] = $gatewayRes['message'];
                }
            }

            $paymentData['amount'] = $order->amount_payed;
        }else{
            if($data['amountPayed'] != $order->amount_payed) {
                $paymentData['flag'] = true;
                $paymentData['flagMessage'] = "The amount that should be paid does not match the amount that is reported to have been paid";
            }
            $paymentData['amount'] = $data['amountPayed'];
        }
        
        $paymentData['orderId'] = $order->id;
        $paymentData['receiptNumber'] = Helpers::generateReceiptNo($order->id, Auth::guard("client")->user()->id, $data['processingId']);
        if($data['cardPayment']) $paymentData['reference'] = $data['reference'];
        $paymentData['paymentDate'] = ($data['cardPayment']) ? now() : $data['paymentDate'];
        $paymentData['paymentGatewayId'] = ($data['cardPayment']) ? PaymentMode::cardPayment()->id : PaymentMode::bankTransfer()->id;
        $payment = $this->paymentService->save($paymentData);

        // if the payment gateway marked the payment as a success, deduct the units
        if($order?->package && (!$data['cardPayment'] || ($data['cardPayment'] && !$gatewayRes['paymentError']))) {
            $this->packageService->deductUnits($order->units, $order?->package);

            // if the client was referred to by a staff, add commission to the staff
            if(Auth::guard('client')->user()->referer) {
                // calculate the bonus/commission for the referer and save it
                $commission = $this->commissionService->save(Auth::guard("client")->user()->referer, $order);
            }
            if($data['cardPayment']) {
                $this->uploadReceipt($payment);  

                if(!$gatewayRes['paymentError']) $this->orderService->completeOrder($order, $payment);
            }
        }

        return $payment;
    }

    private function uploadReceipt($payment)
    {
        // generate receipt if the card payment was successful
        try{
            Helpers::generateReceipt($payment->load('paymentMode'));
            // dd('generate receipt');
            $uploadedReceipt = 'files/receipt'.$payment->receipt_no.'.pdf';
            // dd('time to move..'.$uploadedReceipt);
            $response = Helpers::moveUploadedFileToCloud($uploadedReceipt, FileTypes::PDF->value, Auth::guard('client')->user()->id, 
            FilePurpose::PAYMENT_RECEIPT->value, self::$userType, "client-receipts");
            
            if($response['success']) {
                $this->paymentService->update(['receiptFileId' => $response['upload']['file']->id], $payment);
                // dd("got here");
                try{
                    // Send Payment Mail
                    Mail::to($payment->client->email)->send(new NewPayment($payment, $uploadedReceipt));
                }catch(\Exception $e) {
                    Utilities::logStuff("Error Occurred while attempting to send Payment Email..".$e);
                }
            }
        }catch(\Exception $e) {
            Utilities::logStuff("Error Occurred while attempting to generate and upload receipt..".$e);
        }
    }
    
}
