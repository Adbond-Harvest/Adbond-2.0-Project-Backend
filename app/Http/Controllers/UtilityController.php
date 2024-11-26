<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;

use app\Http\Resources\BenefitResource;

use app\Services\UtilityService;

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
}
