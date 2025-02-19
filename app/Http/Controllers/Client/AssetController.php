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

    public function assets(Request $request)
    {
        $page = ($request->query('page')) ?? 1;
        $perPage = ($request->query('perPage'));
        if(!is_int((int) $page) || $page <= 0) $page = 1;
        if(!is_int((int) $perPage) || $perPage==null) $perPage = env('PAGINATION_PER_PAGE');
        $offset = $perPage * ($page-1);

        $filter = [];
        if($request->query('text')) $filter["text"] = $request->query('text');
        if($request->query('date')) $filter["date"] = $request->query('date');
        
        $this->clientPackageService->filter = $filter;
        $assets = $this->clientPackageService->clientAssets(Auth::guard('client')->user()->id, ['package.media'], $offset, $perPage);

        // Get Count
        $this->clientPackageService->count = true;
        $assetsCount = $this->clientPackageService->clientAssets(Auth::guard('client')->user()->id);

        return Utilities::paginatedOkay(AssetResource::collection($assets), $page, $perPage, $assetsCount);
    }

    public function asset($assetId)
    {
        $asset = $this->clientPackageService->clientPackage($assetId, ['package.media']);
        if(!$asset) return Utilities::error402("Asset not found");

        if($asset->client_id != Auth::guard('client')->user()->id) return Utilities::error402("You are not permitted to view this asset");

        return Utilities::ok(new AssetResource($asset));
    }
}
