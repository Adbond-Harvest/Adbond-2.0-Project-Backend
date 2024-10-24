<?php

namespace app\Http\Requests\client;

use Illuminate\Foundation\Http\FormRequest;
use app\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

use app\EnumClass;

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
            "firstname" => "string",
            "lastname" => "string",
            "gender" => ["string",Rule::in(EnumClass::genders())],
            "phoneNumber" => "string|min:8|max:25",
            "countryId" => "integer|nullable",
            "stateId" => "integer|nullable",
            "address" => "string",
            "relationship" => "string",
        ];
    }
}
