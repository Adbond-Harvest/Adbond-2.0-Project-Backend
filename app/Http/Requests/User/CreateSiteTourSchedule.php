<?php

namespace app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use app\Http\Requests\BaseRequest;

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
            "availableDate" => "required|date|after:today",
            "availableTime" => "required|date_format:h:i A",
            "fee" => "required|numeric",
            "slots" => "required|integer"
        ];
    }
}
