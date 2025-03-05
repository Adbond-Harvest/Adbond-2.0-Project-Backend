<?php

namespace app\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use app\Http\Requests\BaseRequest;

use app\EnumClass;

class RequestAssetSwitch extends BaseRequest
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
            "type" => ["required", Rule::in(EnumClass::assetSwitchTypes())],
            "assetId" => "required|integer",
            "toPackageId" => "required|integer|exists:packages,id",
        ];
    }
}
