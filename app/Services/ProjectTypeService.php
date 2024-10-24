<?php

namespace app\Services;

use app\Models\ProjectType;

class ProjectTypeService
{
    public function update($data, $projectType)
    {
        if(isset($data['name'])) $projectType->name = $data['name'];
        if(isset($data['photoId'])) $projectType->file_id = $data['photoId'];
        if(isset($data['description'])) $projectType->description = $data['description'];
        if(isset($data['order'])) $projectType->order = $data['order'];   
        $projectType->update();

        return $projectType;
    }

    public function projectTypes($with=[])
    {
        return ProjectType::with($with)->get();
    }

    public function projectType($id, $with=[])
    {
        return ProjectType::with($with)->where("id", $id)->first();
    }
}