<?php

namespace app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use app\Http\Requests\BaseRequest;

class UpdateProjectType extends BaseRequest
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
            "id" => "required|integer|exists:project_types,id",
            // "photoId" => "nullable|integer",
            "photo" => 'image|max:10000|mimes:jpeg,png,jpg,gif',
            "description" => "nullable|string",
            "order" => "nullable|integer"
        ];
    }
}
