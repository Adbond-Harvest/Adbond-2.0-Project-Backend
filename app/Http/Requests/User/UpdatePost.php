<?php

namespace app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use app\Http\Requests\BaseRequest;

use app\Rules\VideoDuration;

class UpdatePost extends BaseRequest
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
            "topic" => "nullable|string",
            "type" => "nullable|string",
            "file" => ["nullable","file","mimes:jpg,jpeg,png,gif,webp,mp4,mpeg,mov,avi,wmv,webm,flv,3gp,m4v","max:50000", new VideoDuration(60)],
            "content" => "nullable|string",
             "projectTypeId" => "nullable|integer|exists:project_types,id"
        ];
    }
}
