<?php

namespace app\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use app\Http\Requests\BaseRequest;

class CreateOffer extends BaseRequest
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
            "assetId" => "required|integer",
            "price" => "required_without:resellOrderId|numeric",
            "resellOrderId" => "nullable|integer|exists:resell_orders,id"
        ];
    }
}
