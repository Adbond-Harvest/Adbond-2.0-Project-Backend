<?php

namespace app\Services;

use Maatwebsite\Excel\Facades\Excel;
use PDF;

use app\Models\Assessment;
use app\Models\Question;
use app\Models\StaffAssessmentAnswer;

class AssessmentQuestionService
{

    public function save($data)
    {
        $question = new Question;
        $question->assessment_id = $data['assessmentId'];
        $question->question = $data['question'];

        $question->save();

        return $question;
    }

    public function update($data, $question)
    {
        if(isset($data['assessmentId'])) $question->assessment_id = $data['assessmentId'];
        if(isset($data['question'])) $question->question = $data['question'];

        $question->update();

        return $question;
    }

    public function question($id, $with=[])
    {
        return Question::with($with)->where("id", $id)->first();
    }

    public function assessmentQuestions($assessmentId, $with=[])
    {
        return Question::with($with)->where("assessment_id", $assessmentId)->orderBy("created_at", "DESC")->get();
    }

    public function delete($question)
    {
        $question->delete();
    }
}