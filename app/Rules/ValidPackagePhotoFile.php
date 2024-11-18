<?php

namespace app\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use app\Models\File;

use app\Enums\FilePurpose;

class ValidPackagePhotoFile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $file = File::find($value);
        if(!$file) {
            $fail('File Does not exist');
        }else{
            if($file->purpose != FilePurpose::PACKAGE_PHOTO->value) $fail("Photo file id of ". $value ." is not correct");
        }
    }
}
