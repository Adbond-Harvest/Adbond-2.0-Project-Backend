<?php

namespace app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use app\Http\Requests\BaseRequest;

class UpdateAssessment extends BaseRequest
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
            "title" => "nullable|string",
            "description" => "nullable|string",
            "instructions" => "nullable|string",
            "duration" => "nullable|integer",
            "cutOffMark" => "nullable|numeric",
            "active" => "nullable|boolean",
            "questions" => "nullable|array",
            "questions.*" => "array",
            "questions.*.questionId" => "nullable|integer",
            "questions.*.question" => "required|string",
            "questions.*.options" => "required|array",
            "questions.*.options.*" => "array",
            "questions.*.options.*.optionId" => "nullable|integer",
            "questions.*.options.*.value" => "required",
            "questions.*.options.*.answer" => "required|boolean"
        ];
    }
}
