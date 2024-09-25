<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

use App\EnumClass;

class UpdateProfile extends BaseRequest
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
            "photoId" => "nullable|integer|exists:files,id",
            "phoneNumber" => "nullable|string",
            "postalCode" => "nullable|string",
            "gender" => ["nullable","string", Rule::in(EnumClass::genders())],
            "accountNumber" => "nullable|string",
            "accountName" => "nullable|string",
            "bankId" => "nullable|integer|exists:banks,id" 
        ];
    }
}
