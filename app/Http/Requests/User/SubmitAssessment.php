<?php

namespace app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use app\Http\Requests\BaseRequest;

class SubmitAssessment extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "attemptId" => "required|integer",
            "answers" => "required|array",
            "answers.*" => "array",
            "answers.*.questionId" => "required|integer|exists:questions,id",
            "answers.*.selectedOptionId" => "nullable|integer|exists:question_options,id",
        ];
    }
}
