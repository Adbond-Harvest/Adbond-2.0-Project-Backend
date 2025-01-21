<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;

use app\Http\Resources\BenefitResource;
use app\Http\Resources\BankResource;

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
}
