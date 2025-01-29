<?php

namespace app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateSiteTourSchedule extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "projectTypeId" => "required|integer|exists:project_types,id",
            "projectId" => "required|integer|exists:projects,id",
            "packageId" => "required|integer|exists:packages,id",
            "availableDate" => "required|date|after:today",
            "availableTime" => "required|date_format:h:i A" 
        ];
    }
}
