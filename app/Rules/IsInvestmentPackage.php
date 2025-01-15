<?php

namespace app\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use app\Models\Package;
use app\Enums\PackageType;

class IsInvestmentPackage implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $package = Package::find($value);
        if(!$package) $fail("PackageId is not valid");
        if($package) {
            if($package->type != PackageType::INVESTMENT->value) $fail("Not an Investment Package");
        }
    }
}
