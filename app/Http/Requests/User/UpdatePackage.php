<?php

namespace app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use app\Http\Requests\BaseRequest;

use app\Rules\ValidPackageBrochureFile;
use app\Rules\ValidPackageMediaFile;
use app\Rules\PackageNameUnique;

class UpdatePackage extends BaseRequest
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
            "projectId" => "nullable|integer|exists:projects,id",
            "name" => ["nullable","string", new PackageNameUnique()],
            "stateId" => "nullable|integer|exists:states,id",
            "address" => "nullable|string",
            "size" => "nullable|numeric",
            "amount" => "nullable|numeric",
            "units" => "nullable|integer",
            "discount" => "nullable|numeric",
            "minPrice" => "nullable|numeric",
            "installmentDuration" => "nullable|integer",
            "infrastructureFee" => "nullable|numeric",
            "description" => "nullable|string",
            "benefits" => "nullable|array",
            "benefits.*" => "integer|exists:benefits,id",
            // "brochureFileId" => ["nullable", "integer", new ValidPackageBrochureFile()],
            "brochureFile" => "nullable|file|max:10000|mimes:jpeg,png,jpg,pdf,doc,docx",
            "installmentOption" => "nullable|boolean",
            "vrUrl" => "nullable|string",
            "packageMediaIds" => "nullable|array",
            "packageMediaIds.*" => ["integer", new ValidPackageMediaFile()]
        ];
    }
}
