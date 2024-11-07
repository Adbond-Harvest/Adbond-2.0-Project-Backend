<?php

namespace app\Services;


use app\Models\Order;
use app\Models\OrderDiscount;
use app\Models\Promo;
use app\Models\PromoCode;
use app\Models\PromoProduct;

use app\Helpers;

class PromoCodeService
{
    public function validatePromoCode($code, $package)
    {
        $promoCode = PromoCode::whereCode($code)->first();
        if($promoCode->promo) {
            if(!$this->checkExpiry($promoCode)) return ["valid" => false, "message" => "Promo code has expired"];
            
            if(!$this->checkActive($promoCode)) return ["valid" => false, "message" => "The Promo is no longer active"];
            
            if(!$this->checkUsage($promoCode)) return ["valid" => false, "message" => "This Promo Code has reached its maximum usage"];
            
            if(!$this->checkPackage($promoCode, $package)) return ["valid" => false, "message" => "This package is not included in the promo"];
        }else{
            return ["valid" => false, "message" => "Cannot find this Promo"];
        }
        return ["valid" => true, "discount" => $promoCode->promo->discount];
    }

    public function checkExpiry($promoCode)
    {
        return (
                    (!$promoCode->expiry || ($promoCode->expiry > now()))
                    && 
                    (!$promoCode->end || ($promoCode->promo->end > now()))
                );
    }

    public function checkActive($promoCode)
    {
        return ($promoCode->active && $promoCode->promo->active);
    }

    public function checkUsage($promoCode)
    {
        return (!$promoCode->max_usage || ($promoCode->usage_count < $promoCode->max_usage));
    }

    public function checkPackage($promoCode, $package)
    {
        if(!$promoCode->package_limited) return true;
        if(
            ($package->project && in_array($promoCode->promo->id, $package->project->promos()->pluck('promo_id')->toArray()))
            ||
            in_array($promoCode->promo->id, $package->promos()->pluck('promo_id')->toArray())
        ) return true;

        return false;
    }

}