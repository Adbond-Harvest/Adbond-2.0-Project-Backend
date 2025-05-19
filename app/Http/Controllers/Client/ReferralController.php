<?php

namespace app\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Http\Controllers\Controller;

use app\Http\Resources\ClientBriefResource;

use app\Services\CommissionService;

use app\Utilities;

class ReferralController extends Controller
{
    private $commissionService;

    public function __construct()
    {
        $this->commissionService = new CommissionService;
    }

    public function referrals()
    {
        $referrals = Auth::guard("client")->user()->referrals;

        $totalEarnings = $this->commissionService->totalClientEarnings(Auth::guard("client")->user()->id);

        return Utilities::ok([
           "referrals" => ClientBriefResource::collection($referrals),
           "total" => ($totalEarnings) ? $totalEarnings->total_earnings : 0
        ]);
    }
}
