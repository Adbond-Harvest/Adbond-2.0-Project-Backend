<?php

namespace app\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use app\Http\Requests\BaseRequest;

use app\Rules\ValidFile;

use app\Enums\FilePurpose;

class SavePayment extends BaseRequest
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
            "processingId" => "required|integer",
            "cardPayment" => "required|boolean",
            "reference" => "required_if:cardPayment,true",
            "paymentDate" => "required_if:cardPayment,false",
            // "amountPayed" => "required_if:cardPayment,false",
            'evidence' => 'required_if:cardPayment,false|file|max:10000|mimes:jpeg,png,jpg,gif,pdf',
            // "evidenceFileId" => ["required_if:cardPayment,false", new ValidFile(FilePurpose::PAYMENT_EVIDENCE->value)]
        ];
    }
}
