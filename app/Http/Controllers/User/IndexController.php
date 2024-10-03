<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectTypeResource;

use App\Services\ProjectTypeService;
use App\Services\ProjectService;

use App\Utilities;

class IndexController extends Controller
{
    private $projectTypeService;
    private $projectService;

    public function __construct()
    {
        $this->projectTypeService = new ProjectTypeService;
        $this->projectService = new ProjectService;
    }

    public function dashboard(Request $request)
    {
        $projectTypes = $this->projectTypeService->projectTypes();
        $projectTypeObj = null;

        $projectTypesCounts = [];
        if (count($projectTypes) > 0) {
            foreach ($projectTypes as $projectType) {
                if(!$projectTypeObj) $projectTypeObj = $projectType;
                $projectTypesCounts[$projectType->name] = [
                    "totalProjects" => $projectType->projects->count(),
                    "activeProjects" => $projectType->activeProjects->count(),
                    "inactiveProjects" => $projectType->inactiveProjects->count()
                ];
            }
        }
        unset($projectTypeObj['projects']);

        $page = ($request->query('page')) ?? 1;
        $perPage = ($request->query('perPage'));
        if(!is_int((int) $page) || $page <= 0) $page = 1;
        if(!is_int((int) $perPage) || $perPage==null) $perPage = null;
        $offset = $perPage * ($page-1);
        $projects = $this->projectService->projects($projectTypeObj->id, [], $offset, $perPage);

        return Utilities::ok([
            "projectTypes" => $projectTypesCounts,
            "projects" => ProjectResource::collection($projects),
            "activeProjectType" => new ProjectTypeResource($projectTypeObj)
        ]);
    }
}
