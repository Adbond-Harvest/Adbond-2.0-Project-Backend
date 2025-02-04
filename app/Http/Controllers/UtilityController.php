<?php

namespace app\Http\Controllers;

use app\Http\Resources\BankAccountResource;
use Illuminate\Http\Request;

use app\Http\Resources\BenefitResource;
use app\Http\Resources\BankResource;
use app\Http\Resources\WalletBankAccountResource;
use app\Http\Resources\ResellOrderResource;

use app\Services\UtilityService;
use app\Utilities;

class UtilityController extends Controller
{
    private $utilityService;

    public function __construct()
    {
        $this->utilityService = new UtilityService;
    }

    public function benefits()
    {
        $benefits = $this->utilityService->benefits();

        return BenefitResource::collection($benefits);
    }

    public function banks()
    {
        $banks = $this->utilityService->banks();

        return Utilities::ok(BankResource::collection($banks));
    }

    public function bankAccounts()
    {
        $accounts = $this->utilityService->bankAccounts();

        return Utilities::ok(BankAccountResource::collection($accounts));
    }

    public function activeBankAccounts()
    {
        $accounts = $this->utilityService->bankAccounts(1);

        return Utilities::ok(BankAccountResource::collection($accounts));
    }

    public function resellOrders()
    {
        $resellOrders = $this->utilityService->resellOrders();
        return Utilities::ok(ResellOrderResource::collection($resellOrders));
    }
}
