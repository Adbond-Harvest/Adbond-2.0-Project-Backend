<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\PackageResource;

use App\Http\Requests\User\SavePackage;
use App\Http\Requests\User\UpdatePackage;
use App\Http\Requests\User\TogglePackageActivate;
use App\Http\Requests\User\FilterPackage;


use App\Services\PackageService;
use App\Services\FileService;
use App\Services\ProjectService;

use App\Utilities;


class PackageController extends Controller
{
    private $packageService;
    private $projectService;
    private $fileService;

    public function __construct()
    {
        $this->packageService = new PackageService;
        $this->projectService = new ProjectService;
        $this->fileService = new FileService;
    }

    public function packages(Request $request, $projectId)
    {
        if (!is_numeric($projectId) || !ctype_digit($projectId)) return Utilities::error402("Invalid parameter projectID");

        $page = ($request->query('page')) ?? 1;
        $perPage = ($request->query('perPage'));
        if(!is_int((int) $page) || $page <= 0) $page = 1;
        if(!is_int((int) $perPage) || $perPage==null) $perPage = env('PAGINATION_PER_PAGE');
        $offset = $perPage * ($page-1);

        $packages = $this->packageService->packages($projectId, [], $offset, $perPage);
        $this->packageService->count = true;
        $packagesCount = $this->packageService->packages($projectId);

        return Utilities::paginatedOkay(PackageResource::collection($packages), $page, $perPage, $packagesCount);
    }

    public function package($id)
    {
        if (!is_numeric($id) || !ctype_digit($id)) return Utilities::error402("Invalid parameter iD");

        $package = $this->packageService->package($id);
        return Utilities::ok(new PackageResource($package));
    }

    public function save(SavePackage $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->validated();
            $data['userId'] = Auth::user()->id;
            $package = $this->packageService->save($data);
            if(isset($data['packagePhotoIds'])) $this->packageService->savePhotos($data['packagePhotoIds'], $package);
            $package = $this->packageService->package($package->id);
            DB::commit();

            return Utilities::ok(new PackageResource($package));
        }catch(\Exception $e){
            DB::rollBack();
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function update(UpdatePackage $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->validated();
            // Get The package being updated
            $package = $this->packageService->package($data['id']);

            $package = $this->packageService->update($data, $package);

            if(isset($data['packagePhotoIds']) && !empty($data['packagePhotoIds'])) {
                // Get the Photo Ids of the Package
                $currentPhotoIds = $this->packageService->getPackagePhotoIds($package);

                // Get the Files that were added
                $newPhotoIds = array_diff($data['packagePhotoIds'], $currentPhotoIds);
                // Get the Files that were deleted
                $removedPhotoIds = array_diff($currentPhotoIds, $data['packagePhotoIds']);
            }
            // Add and Delete files as necessary
            if(!empty($newPhotoIds)) $this->packageService->savePhotos($newPhotoIds, $package);
            if(!empty($removedPhotoIds)) $this->fileService->deleteFiles($removedPhotoIds);

            $package = $this->packageService->package($package->id, ['project', 'photos']);
            DB::commit();

            return Utilities::ok(new PackageResource($package));
        }catch(\Exception $e){
            DB::rollBack();
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function filter(FilterPackage $request, $projectId)
    {
        try{
            if (!is_numeric($projectId) || !ctype_digit($projectId)) return Utilities::error402("Invalid parameter projectID");
            $page = ($request->query('page')) ?? 1;
            $perPage = ($request->query('perPage'));
            if(!is_int((int) $page) || $page <= 0) $page = 1;
            if(!is_int((int) $perPage) || $perPage==null) $perPage = env('PAGINATION_PER_PAGE');
            $offset = $perPage * ($page-1);

            $filter = $request->validated();
            $this->packageService->projectId = $projectId;
            $packages = $this->packageService->filter($filter, [], $offset, $perPage);
            $this->packageService->count = true;
            $packagesCount = $this->packageService->filter($filter);

            return Utilities::paginatedOkay(PackageResource::collection($packages), $page, $perPage, $packagesCount);
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function search(Request $request, $projectId)
    {
        try{
            $page = ($request->query('page')) ?? 1;
            $perPage = ($request->query('perPage'));
            if(!is_int((int) $page) || $page <= 0) $page = 1;
            if(!is_int((int) $perPage) || $perPage==null) $perPage = env('PAGINATION_PER_PAGE');
            $offset = $perPage * ($page-1);

            $text = ($request->query('text')) ?? null;
            $packages = $this->packageService->search($text, $projectId, $offset, $perPage);
            $this->packageService->count = true;
            $packagesCount = $this->packageService->search($text, $projectId);

            return Utilities::paginatedOkay(PackageResource::collection($packages), $page, $perPage, $packagesCount);
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    
    public function export(Request $request, $projectId)
    {
        try {
            if (!is_numeric($projectId) || !ctype_digit($projectId)) return Utilities::error402("Invalid parameter projectID");
            $project  = $this->projectService->project($projectId);

            if(!$project) return Utilities::error402("Project not found, Project Id provided is invalid");

            $packages = $this->packageService->packages($projectId);

            $type = ($request->query('type')) ?? null;
            if(!$type) return Utilities::error402("Type is required");

            // Handle different export types
            switch ($type) {
                case 'excel':
                    return $this->packageService->exportToExcel($packages);
                case 'pdf':
                    return $this->packageService->exportToPDF($packages, $project->name);
                default:
                    return Utilities::error402("Invalid export type");
            }
        } catch (\Exception $e) {
            return Utilities::error($e, 'An error occurred during export');
        }
    }

    public function markAsSoldOut(TogglePackageActivate $request)
    {
        try{
            $id = $request->validated("id");
            $package = $this->packageService->package($id);
            if(!$package) return Utilities::error402("Package not found");

            if($package->sold_out) return Utilities::error402("Package is already sold out");
            $package = $this->packageService->markAsSoldOut($package);

            return Utilities::okay("Package is marked out sold out", new PackageResource($package));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function markAsInStock(TogglePackageActivate $request)
    {
        try{
            $id = $request->validated("id");
            $package = $this->packageService->package($id);
            if(!$package) return Utilities::error402("Package not found");

            if(!$package->sold_out) return Utilities::error402("Package is already not Sold Out");
            $package = $this->packageService->markAsBackInStock($package);

            return Utilities::okay("Package is marked as back in Stock", new PackageResource($package));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function activate(TogglePackageActivate $request)
    {
        try{
            $id = $request->validated("id");
            $package = $this->packageService->package($id);
            if(!$package) return Utilities::error402("Package not found");

            if($package->active) return Utilities::error402("Package is already active");
            $package = $this->packageService->activate($package);

            return Utilities::okay("Package Activated Successfully", new PackageResource($package));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function deactivate(TogglePackageActivate $request)
    {
        try{
            $id = $request->validated("id");
            $package = $this->packageService->package($id);
            if(!$package) return Utilities::error402("Package not found");

            if(!$package->active) return Utilities::error402("Package is already inactive");
            $package = $this->packageService->deactivate($package);

            return Utilities::okay("Package Deactivated Successfully", new PackageResource($package));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }
}
