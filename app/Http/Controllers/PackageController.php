<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;

use app\Http\Resources\PackageResource;

use app\Services\PackageService;
use app\Utilities;

class PackageController extends Controller
{
    private $packageService;

    public function __construct()
    {
        $this->packageService = new PackageService;
    }

    public function getPackage($packageId)
    {
        if (!is_numeric($packageId) || !ctype_digit($packageId)) return Utilities::error402("Invalid parameter packageID");
        $package = $this->packageService->package($packageId, ['photos', 'state']);

        if(!$package) return Utilities::error402("Package not found");

        return Utilities::ok(new PackageResource($package));
    }

    public function getPackages($projectId)
    {
        if (!is_numeric($projectId) || !ctype_digit($projectId)) return Utilities::error402("Invalid parameter projectID");
        $this->packageService->projectId = $projectId;

        $packages = $this->packageService->packages(['state', 'photos']);

        return Utilities::ok(PackageResource::collection($packages));
    }
}
