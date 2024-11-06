<?php

namespace app\Services;

use app\Models\Order;
use app\Models\Discount;

use app\Enums\OrderDiscountType;

use app\Helpers;
use app\Utilities;

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
            $discountedAmount = Utilities::getDiscountedAmount($discountedAmount, $fullPaymentDiscount);
            $appliedDiscounts[] = [
                "name" => "Full Payment Discount", 
                "type"=>OrderDiscountType::FULL_PAYMENT->value, 
                "discount"=>$fullPaymentDiscount,
                "amount"=>$discountedAmount,
            ];
        }
        if($promoCodeDiscount) {
            $discountedAmount = Utilities::getDiscountedAmount($discountedAmount, $promoCodeDiscount);
            $appliedDiscounts[] = [
                "name" => "Promo Code Discount", 
                "type"=>OrderDiscountType::PROMO->value, 
                "discount"=>$promoCodeDiscount,
                "amount"=>$discountedAmount,
            ];
        }
        if(count($promos) > 0) {
            foreach($promos as $promo) {
                $discountedAmount = Utilities::getDiscountedAmount($discountedAmount, $promo->discount);
                $appliedDiscounts[] = [
                    "name" => $promo->title." Promo", 
                    "type"=>OrderDiscountType::PROMO->value, 
                    "discount"=>$promo->discount,
                    "amount"=>$discountedAmount,
                ];
            }
        }
        return ["appliedDiscounts" => $appliedDiscounts, "amount" => $discountedAmount];
    }

}
