<?php

namespace app\Services;

use app\Models\QuestionOption;
use app\Models\Question;

class AssessmentQuestionOptionService
{

    public function save($data)
    {
        $option = new QuestionOption;
        $option->question_id = $data['questionId'];
        $option->value = $data['value'];
        $option->answer = $data['answer'];

        $option->save();

        return $option;
    }

    public function update($data, $option)
    {
        if(isset($data['questionId'])) $option->question_id = $data['questionId'];
        if(isset($data['value'])) $option->value = $data['value'];
        if(isset($data['answer'])) $option->answer = $data['answer'];

        $option->update();

        return $option;
    }

    public function option($id, $with=[])
    {
        return QuestionOption::with($with)->where("id", $id)->first();
    }

    public function questionOptions($questionId, $with=[])
    {
        return QuestionOption::with($with)->where("question_id", $questionId)->get();
    }

    public function delete($option)
    {
        $option->delete();
    }
}