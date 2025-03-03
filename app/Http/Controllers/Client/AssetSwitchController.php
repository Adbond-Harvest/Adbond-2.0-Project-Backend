<?php

namespace app\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Http\Controllers\Controller;

use app\Http\Requests\Client\RequestDowngrade;

use app\Http\Resources\AssetSwitchRequestResource;

use app\Http\Resources\PackageResource;
use app\Models\ClientPackage;

use app\Services\AssetSwitchService;
use app\Services\PackageService;
use app\Services\ClientPackageService;

use App\Utilities;

class AssetSwitchController extends Controller
{
    private $assetSwitchService;
    private $packageService;
    private $clientPackageService;

    public function __construct()
    {
        $this->assetSwitchService = new AssetSwitchService;
        $this->packageService = new PackageService;
        $this->clientPackageService = new ClientPackageService;
    }

    public function downgradePackages($packageId)
    {
        if ($packageId && (!is_numeric($packageId) || !ctype_digit($packageId))) return Utilities::error402("Invalid parameter packageID");
        $package = $this->packageService->package($packageId);
        if(!$package) return Utilities::error402("Package not found");

        if(!$package?->project) return Utilities::error402("Package Project not found");

        if(!$package?->project?->projectType) return Utilities::error402("Package Project Type not found");

        $packages = $this->assetSwitchService->getDownGradePackages($package);

        return Utilities::ok(PackageResource::collection($packages));
    }

    public function requestDowngrade(RequestDowngrade $request)
    {
        try{
            $data = $request->validated();

            $asset = $this->clientPackageService->clientPackage($data['assetId']);
            if(!$asset) return Utilities::error402("Asset not found");

            if($asset->purchase_complete === 1)return Utilities::error402("This asset is not eligible for downgrade");

            $data['clientId'] = Auth::guard("client")->user()->id;
            $data['fromPackageId'] = $asset->package_id;
            $downgradeRequest = $this->assetSwitchService->requestAssetSwitch($data);

            return Utilities::ok(new AssetSwitchRequestResource($downgradeRequest));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to perform this operation, Please try again later or contact support');
        }
    }
}
