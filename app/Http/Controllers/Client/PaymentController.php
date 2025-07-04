<?php

namespace app\Http\Controllers\Client;

use app\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use app\Http\Requests\Client\SavePayment;
use app\Http\Requests\Client\PrepareAdditionalPayment;
use app\Http\Requests\Client\SaveAdditionalPayment;
use app\Http\Requests\Client\InitializeCardPayment;

use app\Mail\NewOrder;
use app\Mail\NewPayment;

use app\Services\PackageService;
use app\Services\PromoCodeService;
use app\Services\OrderService;
use app\Services\PaymentService;
use app\Services\FileService;
use app\Services\CommissionService;
use app\Services\ClientPackageService;
use app\Services\ClientInvestmentService;

use app\Models\PaymentStatus;
use app\Models\PaymentMode;
use app\Models\PaymentGateway;

use app\Http\Resources\OrderResource;
use app\Http\Resources\PaymentResource;

use app\Enums\PaymentPurpose;
use app\Enums\FilePurpose;
use app\Enums\FileTypes;
use app\Enums\PackageType;
use app\Enums\UserType;
use app\Enums\OrderType;

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
    private $clientPackageService;
    private $clientInvestmentService;

    private static $userType = "app\Models\Client";

    public function __construct()
    {
        $this->paymentService = new PaymentService;
        $this->packageService = new PackageService;
        $this->promoCodeService = new PromoCodeService;
        $this->orderService = new OrderService;
        $this->fileService = new FileService;
        $this->commissionService = new CommissionService;
        $this->clientPackageService = new ClientPackageService;
        $this->clientInvestmentService = new ClientInvestmentService;
    }

    public function initializeCardPayment(InitializeCardPayment $request)
    {
        try{
            $processingId = $request->validated("processingId");
            $processedData = Cache::get('order_processing_' . $processingId);
            if(!$processedData) return Utilities::error402("processing Id has expired.. Go back and prepare the order again");
            $res = $this->paymentService->paystackInit(Auth::guard('client')->user(), $processedData['amountPayable']*100);
            // dd($res);
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

    public function prepareAdditionalPayment(PrepareAdditionalPayment $request)
    {
        $order = $this->orderService->order($request->validated('orderId'));
        if(!$order) return Utilities::error402("Order not found");

        //confirm that its an installment order, else, return error
        if($order->type == OrderType::PURCHASE->value && $order->is_installment == 0) return Utilities::error402("This is not an Installment order, so no additional payments can be made");

        // confirm that the order is not complete
        if($order->completed == 1) return Utilities::error402("This order has already been completed!");

        if($order->installment_count == $order->installments_payed) return Utilities::error402("No more payment is required for this order at this time");

        //get the amount to be paid
        if($order->type == OrderType::PURCHASE->value) {
            $amount = $order->amount_payable/$order->installment_count;
        }else{
            $amount = ($order->is_installment == 1) ? $order->amount_payable/$order->installment_count : $order->amount_payable;
        }
        $processingId = Utilities::getOrderProcessingId();

        // Save in Cache
        Cache::put('order_processing_' . $processingId, ["orderId" => $order->id, "amountPayable" => $amount, "type" => "old"], now()->addHours(12));

        // return the processingId and amount
        return Utilities::ok([
            "processingId" => $processingId,
            "amount" => $amount
        ]);
    }

    public function save(SavePayment $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->validated();
            $processedData = Cache::get('order_processing_' . $data['processingId']);
            if(!$processedData) return Utilities::error402("processing Id has expired.. Go back and prepare the order again");
            $package = $this->packageService->package($processedData['packageId']);
            $order = null;
            $res = null;
            if($data['cardPayment']) { // if it is a card payment
                // verify card payment
                $res = $this->paymentService->paystackVerify($data['reference'], $processedData['amountPayable']);
                // dd($res);

                // set payment Status depending on whether everything checks out in verifying the card payment or not

                if($res['success']==true) {
                    if($res['paymentError']==true) {
                        $data['paymentStatusId'] = PaymentStatus::pending()->id;
                    }else{
                        $data['paymentStatusId'] = ($processedData['isInstallment']) ? PaymentStatus::deposit()->id : PaymentStatus::complete()->id;
                        // $data['balance'] = ($processedData['isInstallment']) ? $processedData['amountDetail']['amount'] - $processedData['amountPayable'] : 0;
                    }
                }else{
                    $data['paymentStatusId'] = PaymentStatus::pending()->id;
                }
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

            if($payment->confirmed == 1) {
                if($order->is_installment==1) {
                    $order = $this->orderService->update(['installmentsPayed' => 1], $order);
                }
            }

            $this->postPaymentActions($data, $processedData, $order, $payment, $res);

            DB::commit();
            $order->load("package")->load("discounts")->load("paymentStatus");

            Cache::forget('order_processing_' . $data['processingId']); // Delete from cache
            
            return Utilities::ok([
                "paymentSummary" => new PaymentResource($payment),
                "order" => new OrderResource($order)
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            return Utilities::error($e, 'An error occurred while trying to perform this operation, Please try again later or contact support');
        }

    }

    private function postPaymentActions($data, $processedData, $order, $payment, $gatewayRes=null)
    {
        // if the payment gateway marked the payment as a success, deduct the units
        if($order?->package && (!$data['cardPayment'] || ($data['cardPayment'] && !$gatewayRes['paymentError']))) {
            // deduct units only if it full payment or the first payment of an installment
            if($order->is_installment == 0 || $order->installments_payed < 2) $this->packageService->deductUnits($order->units, $order?->package);
            if($order->package->type==PackageType::INVESTMENT->value) $clientInvestment = $this->clientInvestmentService->saveInvestment($order, $processedData);

            if($data['cardPayment']) {
                // dd('got here');
                $asset = null;
                
                // If its full payment or its the first installment or its the last installment
                // if the client was referred to by a staff, add commission to the staff
                if(($order->is_installment==0 || ($order->installments_payed < 2 || $order->payment_status_id==PaymentStatus::complete()->id)) && Auth::guard('client')->user()->referer) {
                    // calculate the bonus/commission for the referer and save it
                    if($order->payment_status_id==PaymentStatus::complete()->id && Auth::guard("client")->user()->referer_type == UserType::CLIENT->value) {
                        $this->commissionService->saveClientEarning(Auth::guard("client")->user()->referer, $order);
                    }
                    if(Auth::guard("client")->user()->referer_type == UserType::USER->value) {
                        $this->commissionService->save(Auth::guard("client")->user()->referer, $order);
                    }
                }

                $this->paymentService->uploadReceipt($payment, Auth::guard('client')->user());  
                $clientInvestment = (isset($clientInvestment)) ? $clientInvestment : null;
                if ($order->is_installment == 0 || $order->installments_payed == $order->installment_count) {
                    $this->orderService->completeOrder($order, $payment, $clientInvestment);
                }else{
                    $asset = (($order->package->type==PackageType::INVESTMENT->value) ? $this->clientPackageService->saveClientPackageInvestment($clientInvestment) : $this->clientPackageService->saveClientPackageOrder($order));
                }
            }else{
                (($order->package->type==PackageType::INVESTMENT->value) ? $this->clientPackageService->saveClientPackageInvestment($clientInvestment) : $this->clientPackageService->saveClientPackageOrder($order));
            }
            // dd('skipped'.$data);
        }
    }

    public function saveAdditionalPayment(SaveAdditionalPayment $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->validated();
            $processedData = Cache::get('order_processing_' . $data['processingId']);
            if(!$processedData) return Utilities::error402("processing Id has expired.. Go back and prepare the order again");
            $processedData['isInstallment'] = true;
            $order = $this->orderService->order($processedData['orderId']);
            if(!$order) return Utilities::error402("Order not found");

            if($order->type == OrderType::PURCHASE->value || $order->is_installment==1) {
                $data['installmentsPayed'] = $order->installments_payed + 1;
            }

            if($data['cardPayment']) { // if it is a card payment
                // verify card payment
                $res = $this->paymentService->paystackVerify($data['reference'], $processedData['amountPayable']);
                // dd($res);
                if($res['success']==true) {
                    // if(!$res['paymentError']) {
                        $data['paymentStatusId'] = (($order->type == OrderType::PURCHASE->value || $order->is_installment==1) && ($data['installmentsPayed'] < $order->installment_count)) ? PaymentStatus::deposit()->id : PaymentStatus::complete()->id;
                        $data['amountPayed'] = $order->amount_payed + $processedData['amountPayable'];
                        $data['balance'] = $order->balance - $processedData['amountPayable'];

                        //update order
                        $order = $this->orderService->update($data, $order);
                    // }

                }
                // Save the payment
                $payment = $this->savePayment($order, $data, $processedData, $res);
                // dd('success false');

                // Send email to client about the receipt

            }else{ // if its a bank payment

                //update order
                $order = $this->orderService->update($data, $order);

                // Save Payment evidence
                $purpose = FilePurpose::PAYMENT_EVIDENCE->value;
                $fileType = (str_starts_with($request->file('evidence')->getMimeType(), 'image/')) ? 'image' : 'pdf';
                // dd(str_starts_with($request->file('evidence')->getMimeType(), 'image/'));
                // dd($request->file('evidence')->isValid());
                $res = $this->fileService->save($request->file('evidence'), $fileType, Auth::guard('client')->user()->id, $purpose, self::$userType, 'payment-evidences');
                if($res['status'] != 200) return Utilities::error402('Sorry Payment Evidence could not be uploaded '.$res['message']);
                // dd($res);

                $data['evidenceFileId'] = $res['file']->id;
                $data['paymentStatusId'] = PaymentStatus::pending()->id;
                $payment = $this->savePayment($order, $data, $processedData);
            }

            $this->postPaymentActions($data, $processedData, $order, $payment, $res);

            DB::commit();
            Cache::forget('order_processing_' . $data['processingId']); // Delete from cache

            return Utilities::ok(new PaymentResource($payment));
        }catch(\Exception $e){
            DB::rollBack();
            return Utilities::error($e, 'An error occurred while trying to perform this operation, Please try again later or contact support');
        }
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
        // if(isset($data['balance'])) {
        //     $data['amountPayed'] = $processedData['amountPayable'];
        // }
        // $data['amountPayed'] = $processedData['amountPayable'];
        $data['unitPrice'] = $package->amount;
        // $data['balance'] = ($data['isInstallment']) ? ($data['amountPayable'] - $data['amountPayed']) : 0;
        $data['orderDate'] = (isset($data['orderDate'])) ? $data['orderDate'] : now();
        if($data['isInstallment'] && $package->installment_duration) $data['paymentDueDate'] = now()->addMonths((int)$package->installment_duration);

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
                if(!$gatewayRes['paymentError']) {
                    $paymentData['confirmed'] = true;
                }else{
                    $paymentData['flag'] = true;
                    $paymentData['flagMessage'] = $gatewayRes['message'];
                }
            }else{
                $paymentData['failureMessage'] = $gatewayRes['message'];
                $paymentData['success'] = false;
            }

            $paymentData['amount'] = ($gatewayRes && isset($gatewayRes['amount'])) ? $gatewayRes['amount'] : $processedData['amountPayable'];
        }else{
            // if($data['amountPayed'] != $order->amount_payed) {
            //     $paymentData['flag'] = true;
            //     $paymentData['flagMessage'] = "The amount that should be paid does not match the amount that is reported to have been paid";
            // }
            $paymentData['amount'] = $processedData['amountPayable'];
            $paymentData['evidenceFileId'] = $data['evidenceFileId'];
        }
        
        $paymentData['purchaseId'] = $order->id;
        $paymentData['purchaseType'] = "app\Models\Order";
        $paymentData['receiptNumber'] = Helpers::generateReceiptNo($order->id, Auth::guard("client")->user()->id, $data['processingId']);
        if($data['cardPayment']) $paymentData['reference'] = $data['reference'];
        $paymentData['paymentDate'] = ($data['cardPayment']) ? now() : $data['paymentDate'];
        $paymentData['paymentGatewayId'] = ($data['cardPayment']) ? PaymentMode::cardPayment()->id : PaymentMode::bankTransfer()->id;
        $payment = $this->paymentService->save($paymentData);

        
        // dd($gatewayRes['paymentError']);

        return $payment;
    }
    
}
