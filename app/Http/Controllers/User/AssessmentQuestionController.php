<?php

namespace app\Http\Controllers\User;

use Illuminate\Http\Request;
use app\Http\Controllers\Controller;

use App\Http\Requests\User\SaveAssessmentQuestion;
use App\Http\Requests\User\UpdateAssessmentQuestion;

use App\Http\Resources\QuestionResource;

use App\Services\AssessmentQuestionService;

use App\Utilities;

class AssessmentQuestionController extends Controller
{
    private $assessmentQuestionService;

    public function __construct()
    {
        $this->assessmentQuestionService = new AssessmentQuestionService;
    }

    public function save(SaveAssessmentQuestion $request)
    {
        try{
            $data = $request->validated();
            $question = $this->assessmentQuestionService->save($data);

            return Utilities::ok(new QuestionResource($question));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function update(UpdateAssessmentQuestion $request, $questionId)
    {
        try{
            if (!is_numeric($questionId) || !ctype_digit($questionId)) return Utilities::error402("Invalid parameter questionID");

            $question = $this->assessmentQuestionService->question($questionId);
            if(!$question) return Utilities::error402("Question not found");

            $data = $request->validated();
            $question = $this->assessmentQuestionService->update($data, $question);

            return Utilities::ok(new QuestionResource($question));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function assessmentQuestions($assessmentId)
    {
        if (!is_numeric($assessmentId) || !ctype_digit($assessmentId)) return Utilities::error402("Invalid parameter assessmentID");

        $questions = $this->assessmentQuestionService->assessmentQuestions($assessmentId);

        return Utilities::ok(QuestionResource::collection($questions));
    }

    public function question($questionId)
    {
        if (!is_numeric($questionId) || !ctype_digit($questionId)) return Utilities::error402("Invalid parameter questionID");

        $question = $this->assessmentQuestionService->question($questionId);
        if(!$question) return Utilities::error402("Question not found");

        return Utilities::ok(new QuestionResource($question));
    }

    public function delete($questionId)
    {
        if (!is_numeric($questionId) || !ctype_digit($questionId)) return Utilities::error402("Invalid parameter questionID");

        $question = $this->assessmentQuestionService->question($questionId);
        if(!$question) return Utilities::error402("Question not found");

        $this->assessmentQuestionService->delete($question);

        return Utilities::okay("Question Deleted Successfully");
    }
}
