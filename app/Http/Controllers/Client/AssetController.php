<?php

namespace app\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Http\Controllers\Controller;

use app\Http\Resources\ClientAssetSummaryResource;
use app\Http\Resources\ClientAssetResource;
use app\Http\Resources\AssetResource;

use app\Services\ClientPackageService;

use app\Utilities;

class AssetController extends Controller
{
    private $clientPackageService;

    public function __construct()
    {
        $this->clientPackageService = new ClientPackageService;
    }

    public function summary()
    {
        $summary = $this->clientPackageService->clientAssetSummary(Auth::guard('client')->user()->id);

        return Utilities::ok(new ClientAssetSummaryResource($summary));
    }

    public function assets()
    {
        $assets = $this->clientPackageService->clientAssets(Auth::guard('client')->user()->id);

        return Utilities::ok(AssetResource::collection($assets));
    }

    public function asset($assetId)
    {
        $asset = $this->clientPackageService->clientPackage($assetId);
        if(!$asset) return Utilities::error402("Asset not found");

        if($asset->client_id != Auth::guard('client')->user()->id) return Utilities::error402("You are not permitted to view this asset");

        return Utilities::ok(new AssetResource($asset));
    }
}
