<?php

namespace app\Http\Controllers\Client;

use Illuminate\Http\Request;
use app\Http\Controllers\Controller;

use app\Http\Requests\client\ValidatePromoCode;

use app\Services\PromoCodeService;
use app\Services\PackageService;

use app\Utilities;

class PromoController extends Controller
{
    private $promoCodeService;
    private $packageService;

    public function __construct()
    {
        $this->promoCodeService = new PromoCodeService;
        $this->packageService = new PackageService;
    }

    public function validate(ValidatePromoCode $request)
    {
        // try{
            $data = $request->validated();
            $package = $this->packageService->package($data['packageId']);
            if(!$package) return Utilities::error402("Package was not found");

            $res = $this->promoCodeService->validatePromoCode($data['code'], $package);
            return Utilities::ok($res);

        // }catch(\Exception $e){
        //     return Utilities::error($e, 'An error occurred while trying to send verification mail, Please try again later or contact support');
        // }
    }
}
