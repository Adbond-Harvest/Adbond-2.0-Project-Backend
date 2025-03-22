<?php

namespace app\Services;

use Maatwebsite\Excel\Facades\Excel;
use PDF;

use app\Models\Assessment;

class AssessmentService
{

    public function save($data)
    {
        $assessment = new Assessment;
        $assessment->title = $data['title'];
        $assessment->description = $data['description'];
        if(isset($data['active'])) $assessment->active = $data['active'];

        $assessment->save();

        return $assessment;
    }

    public function update($data, $assessment)
    {
        if(isset($data['title'])) $assessment->title = $data['title'];
        if(isset($data['description'])) $assessment->description = $data['description'];
        if(isset($data['active'])) $assessment->active = $data['active'];

        $assessment->update();

        return $assessment;
    }

    public function assessment($id, $with=[])
    {
        return Assessment::with($with)->where("id", $id)->first();
    }

    public function assessments($with=[])
    {
        return Assessment::with($with)->orderBy("created_at", "DESC")->get();
    }

    public function delete($assessment)
    {
        $assessment->delete();
    }
}