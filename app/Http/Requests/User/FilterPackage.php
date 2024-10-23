<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;
use App\Enums\ProjectFilter;

class FilterPackage extends BaseRequest
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
            "date" => "nullable|date",
            "status" => ["nullable", Rule::in([ProjectFilter::ACTIVE->value, ProjectFilter::INACTIVE->value])]
        ];
    }
}
