<?php

namespace app\Services;

use app\Http\Resources\QuestionResource;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

use app\Models\AssessmentAttempt;
use app\Models\AssessmentAttemptAnswer;

use App\Services\AssessmentQuestionService;
use App\Services\AssessmentQuestionOptionService;

use App\Utilities;

class AssessmentAttemptService
{
    public function save($data)
    {
        $attempt = new AssessmentAttempt;
        $attempt->assessment_id = $data['assessmentId'];
        $attempt->firstname = $data['firstname'];
        if(isset($data['surname'])) $attempt->surname = $data['surname'];
        $attempt->email = $data['email'];
        if(isset($data['phoneNumber'])) $attempt->phone_number = $data['phoneNumber'];
        if(isset($data['gender'])) $attempt->gender = $data['gender'];
        if(isset($data['address'])) $attempt->address = $data['address'];
        if(isset($data['occupation'])) $attempt->occupation = $data['occupation'];
        if(isset($data['referralCode'])) $attempt->referral_code = $data['referralCode'];
        if(isset($data['cutOffMark'])) $attempt->cut_off_mark = $data['cutOffMark'];
        $attempt->total_question = $data['totalQuestion'];

        $attempt->save();

        return $attempt;
    }

    public function update($data, $attempt)
    {
        if(isset($data['startedAt'])) $attempt->started_at = $data['startedAt'];
        if(isset($data['address'])) $attempt->address = $data['address'];
        if(isset($data['occupation'])) $attempt->occupation = $data['occupation'];

        $attempt->update();

        return $attempt;
    }

    public function grade($answers, $attempt)
    {
        if(is_array($answers) && count($answers) > 0) {
            $correctAnswers = 0;
            $questionService = new AssessmentQuestionService;
            $optionService = new AssessmentQuestionOptionService;
            foreach($answers as $answer) {
                $assessmentAnswer = new AssessmentAttemptAnswer;
                $question = $questionService->question($answer['questionId']);
                $assessmentAnswer->attempt_id = $attempt->id;
                $assessmentAnswer->question_id = $answer['questionId'];
                $assessmentAnswer->answer = $optionService->option($answer['selectedOptionId'])?->value;
                $assessmentAnswer->question = $question->question;
                $assessmentAnswer->correct_answer = $question->correctOption->value;
                $assessmentAnswer->correct = ($answer['selectedOptionId'] == $question->correctOption->id) ? true : false;
                $assessmentAnswer->save();

                if($assessmentAnswer->correct == 1) $correctAnswers++;
            }
            $attempt->score = Utilities::getPercentage($correctAnswers, $attempt->assessment->questions->count());
            $attempt->update();
        }
    }

    public function assessmentAttempts($assessmentId)
    {
        return AssessmentAttempt::where("assessment_id", $assessmentId)->orderBy("created_at", "DESC")->get();
    }

    public function attempts($with=[])
    {
        return AssessmentAttempt::with($with)->orderBy("created_at", "DESC")->get();
    }

    public function attempt($attemptId)
    {
        return AssessmentAttempt::find($attemptId);
    }

}