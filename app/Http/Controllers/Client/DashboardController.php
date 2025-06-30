<?php

namespace app\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Http\Controllers\Controller;

use app\Http\Resources\AssetSummaryResource;
use app\Http\Resources\AssetResource;
use app\Http\Resources\ProjectMinResource;
use app\Http\Resources\TransactionResource;

use app\Services\AssetService;
use app\Services\ProjectService;
use app\Services\TransactionService;

use app\Utilities;

class DashboardController extends Controller
{
    private $assetService;
    private $projectService;
    private $transactionService;

    public function __construct()
    {
        $this->assetService = new AssetService;
        $this->projectService = new ProjectService;
        $this->transactionService = new TransactionService;
    }

    public function index()
    {
        try{
            $this->transactionService->clientId = Auth::guard('client')->user()->id;
            $assetSummary = $this->assetService->clientAssetSummary(Auth::guard('client')->user()->id);
            $assets = $this->assetService->clientAssets(Auth::guard('client')->user()->id);
            $projects = $this->projectService->activeProjects(['projectType'], 0, 10);
            $transactions = $this->transactionService->transactions([], 0, 10);
            return [
                "assetSummary" => new AssetSummaryResource($assetSummary),
                "assets" => AssetResource::collection($assets),
                "projects" => ProjectMinResource::collection($projects),
                "transactions" => TransactionResource::collection($transactions)
            ];
        }catch(\Exception $e){
            return Utilities::error($e, 'Oops! n error occurred while trying to fetch data, Please try again later or contact support');
        }
    }
}
