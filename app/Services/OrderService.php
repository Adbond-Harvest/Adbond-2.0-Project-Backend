<?php

namespace app\Services;

use app\Models\Order;
use app\Models\Discount;
use app\Models\OrderDiscount;

use app\Enums\OrderDiscountType;
use app\Enums\FilePurpose;
use app\Enums\ClientPackageOrigin;
use app\Enums\FileTypes;

use app\Helpers;
use app\Utilities;

use app\Services\ClientPackageService;
use app\Services\CommissionService;

/**
 * Order service class
 */
class OrderService
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
                $discountArr = Utilities::getDiscount($discountedAmount, $promo->discount);
                $discountedAmount = $discountArr['amount'];
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
        $order = new Order;
        $order->client_id = $data['clientId'];
        $order->package_id = $data['packageId'];
        $order->units = $data['units'];
        $order->amount_payed = $data['amountPayed'];
        $order->amount_payable = $data['amountPayable'];
        $order->unit_price = $data['unitPrice'];
        if(isset($data['promoCodeId'])) $order->promo_code_id = $data['promoCodeId'];
        $order->is_installment = $data['isInstallment'];
        if($data['isInstallment']) $order->installment_count = $data['installmentCount'];
        if(isset($data['installmentsPayed'])) $order->installments_payed = $data['installmentsPayed'];
        $order->balance = $data['balance'];
        $order->payment_status_id = $data['paymentStatusId'];
        $order->order_date = $data['orderDate'];
        if(isset($data['paymentDueDate'])) $order->payment_due_date = $data['paymentDueDate'];
        if(isset($data['gracePeriodEndDate'])) $order->grace_period_end_date = $data['gracePeriodEndDate'];
        if(isset($data['paymentPeriodStatusId'])) $order->payment_period_status_id = $data['paymentPeriodStatusId'];

        $order->save();

        return $order;
    }

    public function saveOrderDiscounts($order, $discounts)
    {
        foreach($discounts as $discount) {
            $orderDiscount = new OrderDiscount;
            $orderDiscount->order_id = $order->id;
            $orderDiscount->type = $discount['type'];
            $orderDiscount->discount = $discount['discount'];
            $orderDiscount->amount = $discount['discountedAmount'];
            $orderDiscount->description = $discount['name'];
            $orderDiscount->save();
        }
    }


    public function completeOrder($order, $payment)
    {
        $contractFileId = null;
        $letterOfHappinessFileId = null;
        try{
            // generate and save contract
            Helpers::generateContract($order);
            // dd('generate receipt');
            $uploadedContract = "files/contract_{$order->id}.pdf";
            
            $response = Helpers::moveUploadedFileToCloud($uploadedContract, FileTypes::PDF->value, $order->client->id, 
            FilePurpose::CONTRACT->value, "app\Models\Client", "client-contracts");
            
            if($response['success']) $contractFileId = $response['upload']['file']->id;
            
        }catch(\Exception $e) {
            Utilities::logStuff("Error Occurred while attempting to generate and upload contract..".$e);
        }
        // generate and save letter of happiness
        try{
            // generate and save contract
            Helpers::generateLetterOfHappiness($payment);
            // dd('generate receipt');
            $uploadedLetter = "files/letter_of_happiness_{$payment->order->id}.pdf";
            
            $response = Helpers::moveUploadedFileToCloud($uploadedLetter, FileTypes::PDF->value, $order->client->id, 
            FilePurpose::LETTER_OF_HAPPINESS->value, "app\Models\Client", "client-letter_of_happiness");
            
            if($response['success']) $letterOfHappinessFileId = $response['upload']['file']->id;
            
        }catch(\Exception $e) {
            Utilities::logStuff("Error Occurred while attempting to generate and upload letter of happiness..".$e);
        }

        // mark the order as complete
        $order->completed = true;
        $order->update();

        // save the clientPackage and return it
        $clientPackageService = new ClientPackageService;
        $data['clientId'] = $order->client->id;
        $data['packageId'] = $order->package_id;
        $data['origin'] = ClientPackageOrigin::ORDER->value;
        $data['purchaseId'] = $order->id;
        $data['purchaseType'] = Order::$type;
        if($contractFileId) $data['contractFileId'] = $contractFileId;
        if($letterOfHappinessFileId) $data['happinessLetterFileId'] = $letterOfHappinessFileId;
        $clientPackage = $clientPackageService->save($data);

        return $clientPackage;
    }

}
