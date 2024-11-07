<?php

namespace app\Http\Controllers\CLient;

use Illuminate\Http\Request;
use app\Http\Controllers\Controller;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

use app\Http\Requests\Client\PrepareOrder;

use app\Services\OrderService;
use app\Services\PromoCodeService;
use app\Services\PromoService;
use app\Services\PackageService;

use app\Utilities;

class OrderController extends Controller
{
    private $orderService;
    private $promoCodeService;
    private $promoService;
    private $packageService;

    public function __construct()
    {
        $this->orderService = new OrderService;
        $this->packageService = new PackageService;
        $this->promoCodeService = new PromoCodeService;
        $this->promoService = new PromoService;
    }

    public function prepareOrder(PrepareOrder $request)
    {
        // try{
            $data = $request->validated();
            $package = $this->packageService->package($data['packageId']);
            if(!$package) return Utilities::error402("Package was not found");

            $promoCodeDiscount = (isset($data['promoCode'])) ? $this->promoCodeService->validatePromoCode($data['code'], $package)['discount'] : null;
            $promos = $this->promoService->getPromos($package, Auth::guard('client')->user());
            $processingData = ["amount" => ($package->amount * $data['units']), "isInstallment" => $data['isInstallment']];
            $amountDetail = $this->orderService->getPayable($processingData, $promos, $promoCodeDiscount);

            $processingId  = (isset($data['processingId'])) ? $data['processingId'] : Utilities::getOrderProcessingId();
            
            $data['amountDetail'] = $amountDetail;
            $data['amountPayable'] = ($data['isInstallment']) ? ($package->amount/$data['installmentCount']) : $amountDetail['amount'];
            // Cache this data to be used to complete the order processing
            if(isset($data['processingId'])) Cache::forget('order_processing_' . $processingId);
            Cache::put('order_processing_' . $processingId, $data, now()->addHours(12));

            return Utilities::ok([
                "processingId" => $processingId,
                "package" => $package->name,
                "units" => $data['units'],
                "amountPerUnit" => $package->amount,
                "totalAmount" => $package->amount * $data['units'],
                "amountPayable" => $data['amountPayable'],
                "appliedDiscounts" => $amountDetail
            ]);
        // }catch(\Exception $e){
        //     return Utilities::error($e, 'An error occurred while trying to send verification mail, Please try again later or contact support');
        // }
    }
}