<?php

namespace app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use app\Http\Requests\BaseRequest;

class SaveAssessmentQuestion extends BaseRequest
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
            "question" => "required|string",
            "assessmentId" => "required|integer|exists:assessments,id",
            "options" => "required|array",
            "options.*" => "array",
            "options.*.value" => "required",
            "options.*.answer" => "required|boolean"
        ];
    }
}
