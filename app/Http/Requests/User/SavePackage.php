<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;

use App\Rules\ValidPackageBrochureFile;
use App\Rules\ValidPackagePhotoFile;
use App\Rules\PackageNameUnique;

class SavePackage extends BaseRequest
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
            "projectId" => "required|integer|exists:projects,id",
            "name" => ["required","string", new PackageNameUnique()],
            "stateId" => "required|integer|exists:states,id",
            "address" => "nullable|string",
            "size" => "required|numeric",
            "amount" => "required|numeric",
            "units" => "required|integer",
            "discount" => "nullable|numeric",
            "minPrice" => "nullable|numeric",
            "installmentDuration" => "nullable|integer",
            "infrastructureFee" => "nullable|numeric",
            "description" => "nullable|string",
            "benefits" => "nullable|array",
            "benefits.*" => "string",
            "brochureFileId" => ["nullable", "integer", new ValidPackageBrochureFile()],
            "installmentOption" => "nullable|boolean",
            "vrUrl" => "nullable|string",
            "packagePhotoIds" => "nullable|array",
            "packagePhotoIds.*" => ["integer", new ValidPackagePhotoFile()]
        ];
    }
}
