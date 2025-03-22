<?php

namespace app\Services;

use app\Http\Resources\QuestionResource;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

use app\Models\StaffAssessmentAttempt;
use app\Models\StaffAssessmentAnswer;

use App\Services\AssessmentQuestionService;

use App\Utilities;

class AssessmentAttemptService
{
    public function save($data)
    {
        $attempt = new StaffAssessmentAttempt;
        $attempt->assessment_id = $data['assessmentId'];
        $attempt->firstname = $data['firstname'];
        if(isset($data['lastname'])) $attempt->lastname = $data['lastname'];
        $attempt->email = $data['email'];
        if(isset($data['phoneNumber'])) $attempt->phone_number = $data['phoneNumber'];
        if(isset($data['gender'])) $attempt->gender = $data['gender'];
        if(isset($data['address'])) $attempt->address = $data['address'];
        if(isset($data['occupation'])) $attempt->occupation = $data['occupation'];
        if(isset($data['referralCode'])) $attempt->referral_code = $data['referralCode'];

        $attempt->save();

        return $attempt;
    }

    public function grade($answers, $attempt)
    {
        if(is_array($answers) && count($answers) > 0) {
            $correctAnswers = 0;
            $questionService = new AssessmentQuestionService;
            foreach($answers as $answer) {
                $assessmentAnswer = new StaffAssessmentAnswer;
                $question = $questionService->question($answer['questionId']);
                $assessmentAnswer->staff_assessment_attempt_id = $attempt->id;
                $assessmentAnswer->question_id = $answer['questionId'];
                $assessmentAnswer->selected_option_id = $answer['selectedOptionId'];
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

    public function attempts($with=[])
    {
        return StaffAssessmentAttempt::with($with)->orderBy("created_at", "DESC")->get();
    }

}