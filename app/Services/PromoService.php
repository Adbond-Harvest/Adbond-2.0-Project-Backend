<?php

namespace app\Services;


use app\Models\Order;
use app\Models\OrderDiscount;
use app\Models\Promo;
use app\Models\PromoCode;
use app\Models\PromoProduct;

use app\Helpers;

class PromoService
{
    public function getPromos($package, $user)
    {
        // instantiate applicable promos array; an array that will hold all the promos that are applicable to the package and user
        $applicablePromos = [];
        $packageLimitedPromos = [];

        // Get all active and not expired promos
        $promos = $this->running();

        // Filter out those that are package limited and those that are not and add those that are not into the applicable promos array
        if($promos->count() > 0) {
            foreach($promos as $promo) {
                if($promo->package_limited) {
                    $packageLimitedPromos[] = $promo;
                }else{
                    $applicablePromos[] = $promo;
                }
            }
        }

        //Go through the package Limited promos and add those that are applicable to this package to the applicable promos array
        if(count($packageLimitedPromos) > 0) {
            foreach($packageLimitedPromos as $limitedPromo) {
                if(in_array($package?->project?->id, $limitedPromo->projects()->pluck('id')->toArray())) $applicablePromos[] = $limitedPromo;
                if(in_array($package?->id, $limitedPromo->packages()->pluck('id')->toArray())) $applicablePromos[] = $limitedPromo;
            }
        }

        // return the applicable promos array
        return $applicablePromos;
    }

    public function running($promoCodesIncluded=false)
    {
        return ($promoCodesIncluded) ?
                     Promo::whereActive()->where("start", "<", now())->where("end", ">", now())->get()
                     :
                     Promo::whereActive(true)->where("start", "<", now())->where("end", ">", now())->where("has_promo_code", false)->get();
    }

    public function packageLimited()
    {
        return Promo::whereActive()->where("start", "<", now())->where("end", ">", now())->wherePackageLimited()->get();
    }

}
