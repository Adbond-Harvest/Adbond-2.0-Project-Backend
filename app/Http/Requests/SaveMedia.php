<?php

namespace app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use app\Http\Requests\BaseRequest;
use app\Rules\VideoDuration;

class SaveMedia extends BaseRequest
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
            // 'media' => ["required","file","mimes:jpeg,png,jpg,gif,x-ms-asf,x-flv,flv,video/x-m4v,video/mp4,x-mpegURL,MP2T,3gpp,quicktime,x-msvideo,x-ms-wmv,avi","max:50000", new VideoDuration(60)]
            "media" => "mimes:jpg,jpeg,png,gif,webp,mp4,mpeg,mov,avi,wmv,webm,flv,3gp,m4v"
            // "media" => "required"
        ];
    }
}
