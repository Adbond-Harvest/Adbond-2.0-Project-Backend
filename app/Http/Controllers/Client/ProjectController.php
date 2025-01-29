<?php

namespace app\Http\Controllers\Client;

use Illuminate\Http\Request;
use app\Http\Controllers\Controller;

use app\Http\Resources\ProjectResource;
use app\Http\Resources\Min\ProjectTypeResource;
use app\Http\Resources\PackageResource;

use app\Services\ProjectService;
use app\Services\FileService;
use app\Services\ProjectTypeService;
use app\Services\PackageService;

use app\Enums\ProjectFilter;

use app\Utilities;

class ProjectController extends Controller
{
    
    private $projectService;
    private $projectTypeService;
    private $packageService;

    public function __construct()
    {
        $this->projectService = new ProjectService;
        $this->projectTypeService = new ProjectTypeService;
        $this->packageService = new PackageService;
    }

    public function projects(Request $request, $projectTypeId=null)
    {
        if ($projectTypeId && (!is_numeric($projectTypeId) || !ctype_digit($projectTypeId))) return Utilities::error402("Invalid parameter projectTypeID");

        // $summaryData = $this->summary($projectTypeId);

        if($projectTypeId) $this->projectService->typeId = $projectTypeId;
        $page = ($request->query('page')) ?? 1;
        $perPage = ($request->query('perPage'));
        if(!is_int((int) $page) || $page <= 0) $page = 1;
        if(!is_int((int) $perPage) || $perPage==null) $perPage = env('PAGINATION_PER_PAGE');
        $offset = $perPage * ($page-1);

        $filter = [];
        $filter["status"] = ProjectFilter::ACTIVE->value;
        if($request->query('text')) $filter["text"] = $request->query('text');
        if($request->query('date')) $filter["date"] = $request->query('date');
        // if($request->query('status')) {
        //     $validStatus = ["active" => ProjectFilter::ACTIVE->value, "inactive" => ProjectFilter::INACTIVE->value];
        //     if(!in_array($request->query('status'), $validStatus)) return Utilities::error402("Valid Status are: ".$validStatus['active']." and ".$validStatus['inactive']);
        //     $filter["status"] = $request->query('status');
        // }


        $projects = $this->projectService->filter($filter, ["projectType", "packages"], $offset, $perPage);

        // $projects = $this->projectService->projects(["projectType"], $offset, $perPage);
        $this->projectService->count = true;
        $projectsCount = $this->projectService->filter($filter);

        $meta = [
            "page" => $page,
            "perPage" => $perPage,
            "pages" => ceil($projectsCount/$perPage),
            "total" => $projectsCount
        ];

        return Utilities::ok([
            "projects" => ProjectResource::collection($projects),
            "meta" => $meta
        ]);
        // return Utilities::paginatedOkay(ProjectResource::collection($projects), $page, $perPage, $projectsCount);
    }

    public function summary()
    {
        $projectTypes = $this->projectTypeService->projectTypes();
        $this->projectService->count = true;
        $summary = [];
        $summary['all'] = $this->projectService->projects();
        if($projectTypes->count() > 0) {
            foreach($projectTypes as $projectType) {
                $this->projectService->typeId = $projectType->id;
                $summary[$projectType->name] = $this->projectService->projects();
            }
        }

        return Utilities::ok($summary);
    }

    public function projectSummary($projectTypeId)
    {
        // $this->projectService->typeId = $projectTypeId;
        // $this->packageService->count = true;
        // $projects = $this->projectService->projects();
        // $summary = [];
        // if($projects->count() > 0) {
        //     foreach($projects as $project) {
        //         $this->packageService->projectId = $project->id;
        //         $summary[$project->name] = $this->packageService->packages();
        //     }
        // }
        // return Utilities::ok($summary);
        $this->projectService->typeId = $projectTypeId;
        $summary = $this->projectService->projectPackageSummary();

        return Utilities::ok($summary);
    }

    public function project(Request $request, $projectId)
    {
        if ($projectId && (!is_numeric($projectId) || !ctype_digit($projectId))) return Utilities::error402("Invalid parameter projectID");

        $project = $this->projectService->project($projectId);
        if(!$project) return Utilities::error402("Project not found");

        $this->packageService->projectId = $projectId;

        $this->projectService->typeId = $project->project_type_id;
        $summary = $this->projectService->projectPackageSummary();

        $packages = $this->packageService->packages();

        return Utilities::ok([
            "summary" => $summary,
            "packages" => PackageResource::collection($packages)
        ]);
    }

    public function projectType(Request $request, $projectTypeId)
    {
        if ($projectTypeId && (!is_numeric($projectTypeId) || !ctype_digit($projectTypeId))) return Utilities::error402("Invalid parameter projectTypeID");

        $projectType = $this->projectTypeService->projectType($projectTypeId);
        if(!$projectType) return Utilities::error402("Project Type not found");

        $this->projectService->typeId = $projectTypeId;
        $summary = $this->projectService->projectPackageSummary();

        $packages = $projectType->packages()->get();

        return Utilities::ok([
            "summary" => $summary,
            "packages" => PackageResource::collection($packages)
        ]);
    }
}
