<?php

namespace app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use app\Http\Requests\BaseRequest;

class UpdateUser extends FormRequest
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
            "firstname" => "nullable|string",
            "lastname" => "nullable|string",
            "email" => "nullable|email|unique:users,email",
            "phoneNumber" => "string|max:15|min:11",
            "staffTypeId" => "nullable|integer|exists:staff_types,id",
            "roleId" => "nullable|integer|exists:roles,id",
            "dateJoined" => "nullable|date|before:today"
        ];
    }
}
