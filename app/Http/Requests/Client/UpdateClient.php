<?php

namespace app\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use app\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

use app\EnumClass;

class UpdateClient extends BaseRequest
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
            "firstname" => "string|nullable",
            "lastname" => "string|nullable",
            // "photoId" => "integer|nullable",
            'photo' => 'nullable|image|max:10000|mimes:jpeg,png,jpg,webp',
            "gender" => ["nullable","string", Rule::in(EnumClass::genders())],
            "phoneNumber" => "nullable|string|min:8|max:22",
            "address" => "string|nullable",
            "countryId" => "integer|nullable",
            // "state_id" => "integer",
            "maritalStatus" => ["nullable", "string", Rule::in(EnumClass::maritalStatus())],
            "employmentStatus" => ["nullable","string", Rule::in(EnumClass::employmentStatuses())],
            "occupation" => "string|nullable",
            "identificationId" => "required_with:identificationPhoto|integer|exists:identifications,id",
            "identificationPhoto" => "required_with:identificationId|image|max:10000|mimes:jpeg,png,jpg,webp",
            "postalCode" => "string|nullable",
            "dob" => "date|date_format:Y-m-d",
        ];
    }
}
