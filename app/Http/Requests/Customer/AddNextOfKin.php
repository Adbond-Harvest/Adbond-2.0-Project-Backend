<?php

namespace App\Http\Requests\customer;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

use App\EnumClass;

class AddNextOfKin extends BaseRequest
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
            "title" => "string|nullable",
            "firstname" => "required|string",
            "lastname" => "required|string",
            "gender" => ["required","string",Rule::in(EnumClass::genders())],
            "phoneNumber" => "required|string|min:8|max:25",
            "countryId" => "integer|nullable",
            "stateId" => "integer|nullable",
            "address" => "required|string",
            "relationship" => "required|string",
        ];
    }
}
