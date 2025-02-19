<?php

namespace app\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Http\Controllers\Controller;

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

    public function package($packageId)
    {
        if ($packageId && (!is_numeric($packageId) || !ctype_digit($packageId))) return Utilities::error402("Invalid parameter packageID");

        $package = $this->packageService->package($packageId, ['project.projectType', 'media']);

        if(!$package) return Utilities::error402("Package not found");

        return Utilities::ok(new PackageResource($package));
    }
}
