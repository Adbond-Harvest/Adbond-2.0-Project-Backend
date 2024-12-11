<?php

namespace app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use app\Http\Requests\BaseRequest;

class SaveComment extends BaseRequest
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
            "message" => "required|string",
            "postId" => "required|integer|exists:posts,id"
        ];
    }
}
