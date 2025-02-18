<?php

namespace app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use app\Http\Requests\BaseRequest;

class UpdateSiteTourSchedule extends BaseRequest
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
            "packageId" => "nullable|integer",
            "availableDate" => "nullable|date|after:today",
            "availableTime" => "nullable|date_format:h:i A",
            "fee" => "nullable|numeric",
            "slots" => "nullable|integer"
        ];
    }
}
