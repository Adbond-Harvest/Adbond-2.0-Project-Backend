<?php

namespace app\Http\Controllers\User;

use Illuminate\Http\Request;
use app\Http\Controllers\Controller;

use app\Http\Requests\User\SaveAssessmentQuestionOption;
use app\Http\Requests\User\UpdateAssessmentQuestionOption;

use app\Http\Resources\QuestionOptionResource;

use app\Services\AssessmentQuestionOptionService;

use app\Utilities;

class AssessmentQuestionOptionController extends Controller
{
    private $optionService;

    public function __construct()
    {
        $this->optionService = new AssessmentQuestionOptionService;
    }

    public function save(SaveAssessmentQuestionOption $request)
    {
        try{
            $data = $request->validated();

            $this->optionService->saveQuestionOptions($data['options'], $data['questionId']);

            return Utilities::okay("Question Option(s) Added Successfully");
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function update(UpdateAssessmentQuestionOption $request, $optionId)
    {
        if (!is_numeric($optionId) || !ctype_digit($optionId)) return Utilities::error402("Invalid parameter optionID");

        $data = $request->validated();

        $option = $this->optionService->option($optionId);
        if(!$option) return Utilities::error402("Option not found");

        $this->optionService->update($data, $option);

        return Utilities::okay("Option Updated Successfully");
    }

    public function delete($optionId)
    {
        if (!is_numeric($optionId) || !ctype_digit($optionId)) return Utilities::error402("Invalid parameter optionID");

        $option = $this->optionService->option($optionId);
        if(!$option) return Utilities::error402("Option not found");

        $this->optionService->delete($option);

        return Utilities::okay("Option Deleted Successfully");
    }
}
