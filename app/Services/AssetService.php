<?php

namespace app\Services;

use Maatwebsite\Excel\Facades\Excel;
use PDF;

use app\Models\Package;
use app\Models\ClientPackage;
use app\Models\Order;
use app\Models\ClientAssetsView;

class AssetService
{
    public function clientAssetSummary($clientId)
    {
        return ClientAssetsView::where("client_id", $clientId)->first();
    }

    public function assetsSummary()
    {
        return ClientAssetsView::all();
    }

    public function clientAssets($clientId)
    {
        return ClientPackage::where("sold", false)->where("client_id", $clientId)->whereHAs("purchase")->orderBy("created_at", "DESC")->get();
    }
}