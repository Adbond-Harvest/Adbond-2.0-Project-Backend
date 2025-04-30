<?php

namespace app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use app\Http\Requests\BaseRequest;

use app\EnumClass;

class AddPromoProduct extends BaseRequest
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
            "promoId" => "required|integer|exists:promos,id",
            "products" => "required|array",
            "products.*" => "array",
            "products.*.type" => ["required", "string", Rule::in(EnumClass::promoProductTypes())],
            "products.*.id" => "required|integer",
        ];
    }
}
