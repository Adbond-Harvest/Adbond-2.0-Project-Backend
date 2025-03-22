<?php

namespace app\Http\Controllers\User;

use Illuminate\Http\Request;
use app\Http\Controllers\Controller;

use App\Http\Requests\User\CreateAssessment;
use App\Http\Requests\User\UpdateAssessment;

use App\Http\Resources\AssessmentResource;

use App\Services\AssessmentService;

use App\Utilities;

class AssessmentController extends Controller
{
    private $assessmentService;

    public function __construct()
    {
        $this->assessmentService = new AssessmentService;
    }

    public function create(CreateAssessment $request)
    {
        try{
            $data = $request->validated();
            $assessment = $this->assessmentService->save($data);

            return Utilities::ok(new AssessmentResource($assessment));
        }catch(\Exception $e){
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
}
