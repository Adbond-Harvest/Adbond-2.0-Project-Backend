<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\User\SaveProject;
use App\Http\Requests\User\UpdateProject;
use App\Http\Requests\User\AddProjectLocation;
use App\Http\Requests\User\ToggleProjectActivate;
use App\Http\Requests\User\FilterProject;

use App\Http\Resources\ProjectResource;

use App\Services\ProjectService;
use App\Services\FileService;

use App\Utilities;

class ProjectController extends Controller
{
    private $projectService;
    private $fileService;

    public function __construct()
    {
        $this->projectService = new ProjectService;
        $this->fileService = new FileService;
    }

    public function save(SaveProject $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->validated();
            $project = $this->projectService->save($data);
            $this->projectService->addLocation($project, $data);

            DB::commit();
            return Utilities::okay("Project created Successfully", new ProjectResource($project));
        }catch(\Exception $e){
            DB::rollBack();
            return Utilities::error($e, 'An error occured while trying to process the request, Please try again later or contact support');
        }
    }

    public function update(UpdateProject $request)
    {
        try{
            $data = $request->validated();
            $project = $this->projectService->project($data['id']);
            if(!$project) return Utilities::error402("Project not found");
            if(isset($data['name'])) {
                if($this->projectService->projectNameExists($data['name'], $project)) return Utilities::error402("Project name already exists for this project Type");
            }
            $project = $this->projectService->update($data, $project);
            return Utilities::okay("Project Updated Successfully", new ProjectResource($project));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occured while trying to process the request, Please try again later or contact support');
        }
    }

    public function addLocation(AddProjectLocation $request)
    {
        try{
            $data = $request->validated();
            $project = $this->projectService->project($data['projectId']);
            if(!$project) return Utilities::error402("Project not found");
            $projectLocation = $this->projectService->projectLocationByState($project->id, $data['stateId']);
            if($projectLocation) return Utilities::error402("This Project already exists in this State");
            $this->projectService->addLocation($project, $data);

            return Utilities::okay("Location has been added to Project Successfully");
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occured while trying to process the request, Please try again later or contact support');
        }
    }

    public function projects(Request $request, $projectTypeId)
    {
        if (!is_numeric($projectTypeId) || !ctype_digit($projectTypeId)) return Utilities::error402("Invalid parameter projectTypeID");
        $page = ($request->query('page')) ?? 1;
        $perPage = ($request->query('perPage'));
        if(!is_int((int) $page) || $page <= 0) $page = 1;
        if(!is_int((int) $perPage) || $perPage==null) $perPage = null;
        $offset = $perPage * ($page-1);
        $projects = $this->projectService->projects($projectTypeId, ['type'], $offset, $perPage);

        return Utilities::ok(ProjectResource::collection($projects));
    }

    public function project($id)
    {
        $project = $this->projectService->project($id);
        if(!$project) return Utilities::error402("Project not found");

        return Utilities::ok(new ProjectResource($project));
    }

    public function activate(ToggleProjectActivate $request)
    {
        try{
            $id = $request->validated("id");
            $project = $this->projectService->project($id);
            if(!$project) return Utilities::error402("Project not found");

            if($project->active) return Utilities::error402("Project is already active");
            $project = $this->projectService->activate($project);

            return Utilities::okay("Project Activated Successfully", new ProjectResource($project));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occured while trying to process the request, Please try again later or contact support');
        }
    }

    public function deactivate(ToggleProjectActivate $request)
    {
        try{
            $id = $request->validated("id");
            $project = $this->projectService->project($id);
            if(!$project) return Utilities::error402("Project not found");

            if(!$project->active) return Utilities::error402("Project is already inactive");
            $project = $this->projectService->deactivate($project);

            return Utilities::okay("Project Deactivated Successfully", new ProjectResource($project));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occured while trying to process the request, Please try again later or contact support');
        }
    }

    public function filter(FilterProject $request)
    {
        try{
            $filter = $request->validated();
            $projects = $this->projectService->filter($filter);

            return Utilities::ok(ProjectResource::collection($projects));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occured while trying to process the request, Please try again later or contact support');
        }
    }

    
}
