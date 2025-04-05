<?php

namespace app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use app\Http\Requests\BaseRequest;

class BookSiteTour extends BaseRequest
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
            "siteTourScheduleId" => "required|integer",
            "bookedDate" => "required|date_format:Y-m-d|after:today",
            "firstname" => "required|string",
            "lastname" => "required|string",
            "email" => "required|email",
            "phoneNumber" => "nullable|string",
        ];
    }
}
