<?php

namespace app\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Owenoj\LaravelGetId3\GetId3;

use app\Utilities;

class VideoDuration implements ValidationRule
{
    private $length;

    public function __construct($length)
    {
        $this->length = $length;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $videoMimes = [ 'video/x-ms-asf', 'video/x-flv', 'video/mp4', 'video/x-mpegURL', 'video/MP2T', 'video/3gpp', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv,avi'];
        // dd($value->getMimeType());
        if(in_array($value->getMimeType(), $videoMimes)) {
            $video = new GetId3($value);
            // dd($video->getPlaytimeSeconds());
            // dd('in array');
            if ($video->getPlaytimeSeconds() > $this->length) {
                $fail('The video must maximum of '.Utilities::formatSeconds($this->length));
            }
        }
    }
}

