<?php

namespace app\Http\Controllers\User;

use Illuminate\Http\Request;
use app\Http\Controllers\Controller;

use app\Http\Resources\AssetResource;


use app\Services\ClientPackageService;
use app\Services\AssetService;

use app\Utilities;

class AssetController extends Controller
{
    private $assetService;
    private $clientPackageService;

    public function __construct()
    {
        $this->assetService = new AssetService;   
        $this->clientPackageService = new ClientPackageService;
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
        
        $this->assetService->filter = $filter;
        $assets = $this->assetService->assets(['client'], $offset, $perPage);

        // Get Count
        $this->assetService->count = true;
        $assetsCount = $this->assetService->assets();

        return Utilities::paginatedOkay(AssetResource::collection($assets), $page, $perPage, $assetsCount);
    }
}
