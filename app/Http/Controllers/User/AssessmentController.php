<?php

namespace app\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use app\Http\Controllers\Controller;

use app\Http\Requests\User\CreateAssessment;
use app\Http\Requests\User\UpdateAssessment;

use app\Http\Resources\AssessmentResource;
use app\Http\Resources\AssessmentAttemptResource;

use app\Services\AssessmentService;
use app\Services\AssessmentQuestionService;
use app\Services\AssessmentQuestionOptionService;
use app\Services\AssessmentAttemptService;

use app\Utilities;

class AssessmentController extends Controller
{
    private $assessmentService;
    private $questionService;
    private $optionService;
    private $assessmentAttemptService;

    public function __construct()
    {
        $this->assessmentService = new AssessmentService;
        $this->questionService = new AssessmentQuestionService;
        $this->optionService = new AssessmentQuestionOptionService;
        $this->assessmentAttemptService = new AssessmentAttemptService;
    }

    public function create(CreateAssessment $request)
    {
        try{
            $data = $request->validated();

            DB::beginTransaction();
            
            $assessment = $this->assessmentService->save($data);

            if(isset($data['questions'])) {
                foreach($data['questions'] as $question) {
                    $questionData = ["assessmentId" => $assessment->id, "question" => $question['question']];
                    $questionObj = $this->questionService->save($questionData);
                    $this->optionService->saveQuestionOptions($question['options'], $questionObj->id);
                }
            }

            DB::commit();

            $assessment = $this->assessmentService->assessment($assessment->id);

            return Utilities::ok(new AssessmentResource($assessment));
        }catch(\Exception $e){
            DB::rollBack();
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function update(UpdateAssessment $request, $assessmentId)
    {
        try{
            if (!is_numeric($assessmentId) || !ctype_digit($assessmentId)) return Utilities::error402("Invalid parameter assessmentID");

            $assessment = $this->assessmentService->assessment($assessmentId);
            if(!$assessment) return Utilities::error402("Assessment not found");

            $data = $request->validated();
            $assessment = $this->assessmentService->update($data, $assessment);

            return Utilities::ok(new AssessmentResource($assessment));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function assessments()
    {
        $assessments = $this->assessmentService->assessments();

        return Utilities::ok(AssessmentResource::collection($assessments));
    }

    public function assessment($assessmentId)
    {
        if (!is_numeric($assessmentId) || !ctype_digit($assessmentId)) return Utilities::error402("Invalid parameter assessmentID");

        $assessment = $this->assessmentService->assessment($assessmentId);
        if(!$assessment) return Utilities::error402("Assessment not found");

        return Utilities::ok(new AssessmentResource($assessment));
    }

    public function attempts($assessmentId)
    {
        if (!is_numeric($assessmentId) || !ctype_digit($assessmentId)) return Utilities::error402("Invalid parameter assessmentID");

        $attempts = $this->assessmentAttemptService->assessmentAttempts($assessmentId);

        return Utilities::okay(AssessmentAttemptResource::collection($attempts));
    }
}
