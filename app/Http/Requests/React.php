<?php

namespace app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use app\Http\Requests\BaseRequest;

class React extends BaseRequest
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
            "postId" => "integer|required_without:commentId|exists:posts,id",
            "commentId" => "integer|required_without:postId|exists:comments,id",
            "reaction" => ["required", "string", Rule::in(['like', 'dislike'])]
        ];
    }
}
