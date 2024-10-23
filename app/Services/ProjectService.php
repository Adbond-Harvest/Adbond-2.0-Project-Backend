<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectType;
use App\Models\ProjectLocation;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

use App\Enums\ProjectFilter;
use App\Exports\ProjectExport;

class ProjectService
{
    public $count = false;
    public $typeId = null;

    public function save($data)
    {
        try{
            DB::beginTransaction();
            
            $project = new Project;
            $project->name = $data['name'];
            $project->project_type_id = $data['projectTypeId'];
            if(isset($data['description'])) $project->description = $data['description'];
            $project->save();
            
            $typeCode = strtoupper(substr($project->projectType->name, 0, 3));
            $idCode = str_pad($project->id, 3, '0', STR_PAD_LEFT);
            $project->identifier = "ADB".$typeCode.'-'.$idCode;
            $project->update();

            DB::commit();
            unset($project['projectType']);
            return $project;
        } catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($data, $project)
    {
        if(isset($data['name'])) $project->name = $data['name'];
        if(isset($data['projectTypeId'])) $project->project_type_id = $data['projectTypeId'];
        if(isset($data['description'])) $project->description = $data['description'];
        $project->update();
        return $project;
    }

    public function addLocation($project, $data)
    {
        $projectLocation = new ProjectLocation;
        $projectLocation->project_id = $project->id;
        $projectLocation->state_id = $data['stateId'];
        if(isset($data['address'])) $projectLocation->address = $data['address'];
        $projectLocation->save();
    }

    public function removeLocation($projectLocation)
    {
        $projectLocation->delete();
    }

    public function delete($project)
    {
        if($project->canDelete()) {
            $project->delete();
        }
    }

    public function projects($projectTypeId, $with=[], $offset=0, $perPage=null)
    {
        $query = Project::with($with)->where("project_type_id", $projectTypeId);
        if($this->count) return $query->count();
        if($perPage==null) $perPage=env('PAGINATION_PER_PAGE');
        return $query->orderBy("created_at", "DESC")->offset($offset)->limit($perPage)->get();
    }

    public function project($id, $with=[])
    {
        return Project::with($with)->where("id", $id)->first();
    }

    public function filter($filter, $with=[], $offset=0, $perPage=null)
    {
        $query = Project::with($with);
        if($this->typeId) $query = $query->where("project_type_id", $this->typeId);
        if(isset($filter['date'])) $query = $query->where("created_at", $filter['date']);
        if(isset($filter['status'])) $query = ($filter['status'] == ProjectFilter::ACTIVE->value) ? $query->where("active", true) : $query->where("active", false);
        if($this->count) return $query->count();
        return $query->orderBy("created_at", "DESC")->offset($offset)->limit($perPage)->get();
    }

    public function search($text, $projectTypeId, $offset=0, $perPage=null)
    {
        $query = Project::where("project_type_id", $projectTypeId);
        if($text != null) {
            $query = $query->where(function($q) use($text) { 
                $q->where("identifier", "LIKE", "%".$text."%")->orWhere("name", "LIKE", "%".$text."%");
            });
        }
        if($this->count) return $query->count();
        return $query->orderBy("created_at", "DESC")->offset($offset)->limit($perPage)->get();
    }

    public function exportToExcel($projects, $headingConfig = null)
    {
        return Excel::download(
            new ProjectExport($projects, $headingConfig), 
            'projects-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportToPDF($projects, $headingConfig = null)
    {
        $export = new ProjectExport($projects, $headingConfig);
        $data = [
            'headings' => $export->headings(),
            'projects' => $projects,
            'mappedData' => $projects->map(function ($project) use ($export) {
                return $export->map($project);
            })
        ];

        $pdf = PDF::loadView('exports.pdf.projects', $data);
        return $pdf->download('projects-' . now()->format('Y-m-d') . '.pdf');
    }

    public function projectByName($name, $projectTypeId, $with=[])
    {
        return Project::with($with)->where("project_type_id", $projectTypeId)->where("name", $name)->first();
    }

    public function projectLocation($id, $with=[])
    {
        return ProjectLocation::with($with)->where("id", $id)->first();
    }

    public function projectLocationByState($projectId, $stateId)
    {
        return ProjectLocation::where("project_id", $projectId)->where("state_id", $stateId)->first();
    }

    public function projectNameExists($name, $project)
    {
        $projects = Project::where("project_type_id", $project->project_type_id)->where("name", $name)->get();
        if($projects->count() > 0) {
            if($projects->count() > 1) return true;
            if($projects[0]->id != $project->id) return true;
        }
        return false;
    }

    public function activate($project)
    {
        $project->active = true;
        $project->update();
        return $project;
    }

    public function deactivate($project)
    {
        $project->active = false;
        $project->deactivated_at = now();
        $project->update();
        return $project;
    }
}