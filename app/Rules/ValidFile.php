<?php

namespace app\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use app\Models\File;

class ValidFile implements ValidationRule
{
    private $purpose;

    public function __construct($purpose)
    {
        $this->purpose = $purpose;
    }
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
            if($file->purpose != $this->purpose) $fail("Photo file id of ". $value ." is not correct");
        }
    }
}
