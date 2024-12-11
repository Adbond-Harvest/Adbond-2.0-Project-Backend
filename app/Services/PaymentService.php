<?php

namespace app\Services;

use app\Models\Order;
use app\Models\Payment;
use app\Models\Discount;
use app\Models\PaymentMode;
use app\Models\PaymentGateway;

use app\Enums\OrderDiscountType;

use app\Helpers;
use app\Utilities;

/**
 * Order service class
 */
class PaymentService
{

    public function getPayable($data, $promos, $promoCodeDiscount=null)
    {
        $appliedDiscounts = [];
        $discountedAmount = $data['amount'];
        if(!$data['isInstallment']) {
            $fullPaymentDiscount = Discount::fullPayment()->discount;
            $discountArr = Utilities::getDiscount($discountedAmount, $fullPaymentDiscount);
            $discountedAmount = $discountArr['amount'];
            $appliedDiscounts[] = [
                "name" => "Full Payment Discount", 
                "type"=>OrderDiscountType::FULL_PAYMENT->value, 
                "discount"=>$fullPaymentDiscount,
                "amount"=>$discountArr['amount'],
                "discountedAmount" => $discountArr['discountedAmount']
            ];
        }
        if($promoCodeDiscount) {
            $discountArr = Utilities::getDiscount($discountedAmount, $promoCodeDiscount);
            $discountedAmount = $discountArr['amount'];
            $appliedDiscounts[] = [
                "name" => "Promo Code Discount", 
                "type"=>OrderDiscountType::PROMO->value, 
                "discount"=>$promoCodeDiscount,
                "amount"=>$discountArr['amount'],
                "discountedAmount" => $discountArr['discountedAmount']
            ];
        }
        if(count($promos) > 0) {
            foreach($promos as $promo) {
                $discountedAmount = Utilities::getDiscount($discountedAmount, $promo->discount);
                $appliedDiscounts[] = [
                    "name" => $promo->title." Promo", 
                    "type"=>OrderDiscountType::PROMO->value, 
                    "discount"=>$promo->discount,
                    "amount"=>$discountArr['amount'],
                "discountedAmount" => $discountArr['discountedAmount']
                ];
            }
        }
        return ["appliedDiscounts" => $appliedDiscounts, "amount" => $discountedAmount];
    }

    public function save($data)
    {
        $payment = new Payment;
        $payment->purchase_id = $data['purchaseId'];
        $payment->purchase_type = $data['purchaseType'];
        $payment->client_id = $data['clientId'];
        $payment->amount = $data['amount'];
        $payment->payment_mode_id = $data['paymentModeId'];
        if(isset($data['confirmed'])) $payment->confirmed = $data['confirmed'];
        if(isset($data['evidenceFileId'])) $payment->evidence_file_id = $data['evidenceFileId'];
        if(isset($data['paymentGatewayId'])) $payment->payment_gateway_id = $data['paymentGatewayId'];
        if(isset($data['reference'])) $payment->reference = $data['reference'];
        if(isset($data['success'])) $payment->success = $data['success'];
        if(isset($data['failureMessage'])) $payment->failure_message = $data['failureMessage'];
        if(isset($data['flag'])) $payment->flag = $data['flag'];
        if(isset($data['flagMessage'])) $payment->flag_message = $data['flagMessage'];
        if(isset($data['bankAccountId'])) $payment->bank_account_id = $data['bankAccountId'];
        if(isset($data['paymentDate'])) $payment->payment_date = $data['paymentDate'];
        if(isset($data['receiptFileId'])) $payment->receipt_file_id = $data['receiptFileId'];
        if(isset($data['receiptNumber'])) $payment->receipt_no = $data['receiptNumber'];
        $payment->purpose = $data['purpose'];
        if(isset($data['userId'])) $payment->user_id = $data['userId'];

        $payment->save();

        return $payment;
    }

    public function update($data, $payment)
    {
        if(isset($data['paymentModeId'])) $payment->payment_mode_id = $data['paymentModeId'];
        if(isset($data['evidenceFileId'])) $payment->evidence_file_id = $data['evidenceFileId'];
        if(isset($data['paymentGatewayId'])) $payment->payment_gateway_id = $data['paymentGatewayId'];
        if(isset($data['reference'])) $payment->reference = $data['reference'];
        if(isset($data['success'])) $payment->success = $data['success'];
        if(isset($data['failureMessage'])) $payment->failure_message = $data['failureMessage'];
        if(isset($data['flag'])) $payment->flag = $data['flag'];
        if(isset($data['flagMessage'])) $payment->flag_message = $data['flagMessage'];
        if(isset($data['bankAccountId'])) $payment->bank_account_id = $data['bankAccountId'];
        if(isset($data['paymentDate'])) $payment->payment_date = $data['paymentDate'];
        if(isset($data['receiptFileId'])) $payment->receipt_file_id = $data['receiptFileId'];
        if(isset($data['receiptNumber'])) $payment->receipt_no = $data['receiptNumber'];
        if(isset($data['userId'])) $payment->user_id = $data['userId'];
    }

    public function implementCardPayment($data)
    {
        $data['payment_gateway_id'] = PaymentGateway::paystack()->id;
        $data['payment_mode_id'] = PaymentMode::cardPayment()->id;
        $package = $data['package'];
    }

    public function paystackInit($client, $amount)
    {
        $url = 'https://api.paystack.co/transaction/initialize';
        $headers = [
            "accept" => "application/json",
            "Authorization" => "Bearer ".env('PAYSTACK_SECRET_KEY'),
            "Cache-Control" => "no-cache"
        ];
        $post = ["email" => $client->email, "amount" => $amount];
        $res = ['success' => false];
        $response = Helpers::request($url, $headers, $post);
        if($response['status'] && $response['status'] == true) {
            $res['success'] = true;
            $res['data'] = $response['data'];
        }else{
            $res['message'] = $response['message'];
        }
        return $res;
    }


    public function paystackVerify($reference, $amount)
    {
        $url = 'https://api.paystack.co/transaction/verify/'.$reference;
        $headers = [
            "accept" => "application/json",
            "Authorization" => "Bearer ".env('PAYSTACK_SECRET_KEY'),
            "Cache-Control" => "no-cache"
        ];
        $message = "";
        $res = ['success' => false, 'paymentError' => false];
        $data = Helpers::request($url, $headers);
        if(($data['status'] && $data['status'] == true) && ($data['data'] && $data['data']['status'] == "success")) {
            // dd($data);
            $resData = $data['data'];
            $payedAmount = $resData['amount'];
            $commission = $resData['fees'];
            // dd('payed Amount: '.$payedAmount .' - Commission'. $commission);
            // $productAmount = $payedAmount - $commission; // Get the product amount by deducting paystack commission
            if(($payedAmount == $amount) || abs($payedAmount - $amount) < 1) {// The payed amount is equal to what should be paid or difference not up to 1
                return ["success" => true, "amount" => $payedAmount];
            }else{
                // dd($payedAmount .' == '. $amount . ' || ' . abs($payedAmount - $amount) . ' < 1');
                $res['message'] = "The amount to be paid doesn't match the amount that was paid";
            }
        }else{
            $res['message'] = $data['message'];
            $res['paymentError'] = true;
        }
        return $res;
    }

}
