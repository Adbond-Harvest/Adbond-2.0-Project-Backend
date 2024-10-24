<?php

namespace app\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use app\Models\Package;

class PackageNameUnique implements ValidationRule
{
    public function __construct()
    {
        // $this->projectId = $projectId;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $package = null;
        if(request("id") != null) {
            $package = Package::find(request("id"));
            if(!$package) $fail("Package does not exist");
        }
        $projectId = request("projectId") ?? $package?->project_id;
        $query = Package::where("name", $value)->where("project_id", $projectId);
        if(request("id") != null) $query->where("id", "!=", request("id"));
        $package = $query->first();
        if($package) $fail("Package name already exists");
    }
}
