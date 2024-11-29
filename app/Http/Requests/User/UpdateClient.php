<?php

namespace app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use app\Http\Requests\BaseRequest;

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
            "title" => "nullable|string",
            "firstname" => "nullable|string",
            "lastname" => "nullable|string",
            "othernames" => "nullable|string",
            "photo" => "nullable|image|max:10000|mimes:jpeg,png,jpg",
            "gender" => ["nullable", "string", Rule::in(EnumClass::genders())],
            "phoneNumber" => "nullable|string|max:15|min:11",
            "address" => "nullable|string",
            "state" => "nullable|string",
            "maritalStatus" => ["nullable", "string", Rule::in(EnumClass::maritalStatus())],
            "employmentStatus" => ["nullable", "string", Rule::in(EnumClass::employmentStatuses())],
            "occupation" => "nullable|string",
            "dob" => "nullable|date|before:today"
        ];
    }
}
