<?php

namespace app\Http\Controllers\User;

use Illuminate\Http\Request;
use app\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use app\Http\Requests\User\ConfirmPayment;
use app\Http\Requests\User\DeclinePayment;

use app\Http\Resources\PaymentResource;

use app\Models\PaymentStatus;
use app\Models\Order;

use app\Services\PaymentService;
use app\Services\OrderService;
use app\Services\CommissionService;
use app\Services\ClientPackageService;
use app\Services\ClientInvestmentService;

use app\Enums\PackageType;
use app\Enums\UserType;

use app\Utilities;

class PaymentController extends Controller
{
    private $paymentService;
    private $orderService;
    private $commissionService;
    private $clientPackageService;
    private $clientInvestmentService;

    public function __construct()
    {
        $this->paymentService = new PaymentService;
        $this->orderService = new OrderService;
        $this->commissionService = new CommissionService;
        $this->clientPackageService = new ClientPackageService;
        $this->clientInvestmentService = new ClientInvestmentService;
    }

    public function confirm(ConfirmPayment $request)
    {
        try{
            DB::beginTransaction();
            $payment = $this->paymentService->getPayment($request->validated("paymentId"));
            if(!$payment) return Utilities::error402("Payment not found");

            if($payment->confirmed === 0) return Utilities::error402("This Payment has been rejected, let another request be made");
            if($payment->confirmed === 1) return Utilities::error402("This Payment is already Confirmed");

            $payment = $this->paymentService->confirm($payment);

            $order = $payment->purchase;
            // dd($payment->purchase);
            // update order table to reflect amount_payed and balance;
            $order = $this->orderService->saveAmountPaid($order, $payment->amount);
            $data['paymentStatusId'] = ($order->balance <= 0) ? PaymentStatus::complete()->id : PaymentStatus::deposit()->id;
            // $data['installmentsPayed'] = $order->installments_payed+1;
            $order = $this->orderService->update($data, $order);

            // update staff commission
            if(($order->is_installment==0 || ($order->installments_payed < 2 || $order->payment_status_id==PaymentStatus::complete()->id)) && $payment->client->referer) {
                // calculate the bonus/commission for the referer and save it
                if($order->payment_status_id==PaymentStatus::complete()->id && $payment->client->referer_type == UserType::CLIENT->value) {
                    $this->commissionService->saveClientEarning($payment->client->referer, $order);
                }
                if($payment->client->referer_type == UserType::USER->value) {
                    $this->commissionService->save($payment->client->referer, $order);
                }
            }

            $this->paymentService->uploadReceipt($payment, $payment->client); 
            if($order->is_installment == 0 || $order->installments_payed == $order->installment_count) {
                DB::rollBack();
                $this->orderService->completeOrder($order, $payment);
            }else{
                if($order->installments_payed == 1) {
                    if($order->package->type==PackageType::INVESTMENT->value) {
                        $clientInvestment = $this->clientInvestmentService->getByOrderId($order->id);
                        if($clientInvestment) $this->clientPackageService->saveClientPackageInvestment($clientInvestment);
                    }else{
                        $this->clientPackageService->saveClientPackageOrder($order);
                    }
                }
            }

            DB::commit();

            return Utilities::ok([
                "message" => "payment has been Confirmed",
                "payment" => new PaymentResource($payment)
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function reject(DeclinePayment $request)
    {
        try{
            $payment = $this->paymentService->getPayment($request->validated("paymentId"));
            if(!$payment) return Utilities::error402("Payment not found");

            $payment = $this->paymentService->reject($payment, $request->validated("message"));

            if($payment->purchase_type == Order::$type && $payment->purchase->is_installment == 1) {
                $installmentsPayed = $payment->purchase->installments_payed - 1;
                $this->orderService->update(['installmentsPayed' => $installmentsPayed], $payment->purchase);
            }

            return Utilities::ok([
                "message" => "payment has been Rejected",
                "payment" => new PaymentResource($payment)
            ]);
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function flag(DeclinePayment $request)
    {
        try{
            $payment = $this->paymentService->getPayment($request->validated("paymentId"));
            if(!$payment) return Utilities::error402("Payment not found");

            $payment = $this->paymentService->flag($payment, $request->validated("message"));

            return Utilities::ok([
                "message" => "payment has been Flagged",
                "payment" => new PaymentResource($payment)
            ]);
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }
}
