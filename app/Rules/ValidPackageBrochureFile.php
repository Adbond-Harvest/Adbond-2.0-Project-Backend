<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use App\Models\File;

use App\Enums\FilePurpose;

class ValidPackageBrochureFile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $file = File::find($value);
        if(!$file) $fail('File Does not exist');
        if($file->purpose != FilePurpose::PACKAGE_BROCHURE->value) $fail("Brochure file id is not correct");
    }
}
