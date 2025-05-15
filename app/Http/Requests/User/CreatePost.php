<?php

namespace app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use app\Http\Requests\BaseRequest;

use app\Rules\VideoDuration;
use app\EnumClass;

class CreatePost extends BaseRequest
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
            "topic" => "required|string",
            "type" => ["required", "string", Rule::in(EnumClass::postTypes())],
            "file" => ["required","file","mimes:jpg,jpeg,png,gif,webp,mp4,mpeg,mov,avi,wmv,webm,flv,3gp,m4v","max:50000", new VideoDuration(60)],
            "content" => "required|string",
            "projectTypeId" => "required|integer|exists:project_types,id"
        ];
    }
}
