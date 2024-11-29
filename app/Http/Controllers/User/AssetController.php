<?php

namespace app\Http\Controllers\User;

use Illuminate\Http\Request;
use app\Http\Controllers\Controller;

use app\Http\Resources\AssetResource;


use app\Services\ClientPackageService;

class AssetController extends Controller
{
    private $assetService;

    public function __construct()
    {
        $this->assetService = new AssetService;   
    }

    public function clientAssets(Request $request)
    {

    }
}
