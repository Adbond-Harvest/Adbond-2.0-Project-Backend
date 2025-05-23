<?php

namespace app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use app\Http\Requests\BaseRequest;

class VirtualTeamApplication extends BaseRequest
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
            "firstname" => "required|string",
            "lastname" => "required|string",
            "email" => "required|email|unique:virtual_team_applications,email",
            "location" => "required|string",
            "reason" => "nullable|string"
        ];
    }
     
}
