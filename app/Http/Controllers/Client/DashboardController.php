<?php

namespace app\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Http\Controllers\Controller;

use app\Http\Resources\AssetSummaryResource;
use app\Http\Resources\AssetResource;
use app\Http\Resources\ProjectMinResource;

use app\Services\AssetService;
use app\Services\ProjectService;

use app\Utilities;

class DashboardController extends Controller
{
    private $assetService;
    private $projectService;

    public function __construct()
    {
        $this->assetService = new AssetService;
        $this->projectService = new ProjectService;
    }

    public function index()
    {
        try{
            $assetSummary = $this->assetService->clientAssetSummary(Auth::guard('client')->user()->id);
            $assets = $this->assetService->clientAssets(Auth::guard('client')->user()->id);
            $projects = $this->projectService->activeProjects(['projectType']);
            return [
                "assetSummary" => new AssetSummaryResource($assetSummary),
                "assets" => AssetResource::collection($assets),
                "projects" => ProjectMinResource::collection($projects)
            ];
        }catch(\Exception $e){
            return Utilities::error($e, 'Oops! n error occurred while trying to fetch data, Please try again later or contact support');
        }
    }
}
