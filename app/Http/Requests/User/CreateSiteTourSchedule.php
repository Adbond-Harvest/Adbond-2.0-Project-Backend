<?php

namespace app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use app\Http\Requests\BaseRequest;


use app\EnumClass;

class CreateSiteTourSchedule extends BaseRequest
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
            "packageId" => "required|integer",
            "availableDate" => "nullable|required_without:recurrent|required_if:recurrent,false|date_format:Y-m-d|after:today",
            "availableTime" => "required|date_format:h:i A",
            "recurrent" => "nullable|required_without:availableDate|boolean",
            "recurrentDay" => ["required_if:recurrent,true", Rule::in(EnumClass::weekdays())],
            "fee" => "required|numeric",
            "slots" => "required|integer"
        ];
    }
}
