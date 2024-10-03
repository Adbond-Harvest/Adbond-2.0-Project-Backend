<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Http\Requests\SavePhoto;

use App\Http\Resources\FileResource;

use App\Services\FileService;

use App\Enums\FilePurpose;
use App\Utilities;

class FileController extends Controller
{
    private static $userType = "App\Models\User";
    private $fileService;

    public function __construct()
    {
        $this->fileService = new FileService;
    }

    public function savePhoto(SavePhoto $request)
    {
        try{
            $purpose = $request->validated("purpose");
            
            $res = $this->fileService->save($request->file('photo'), 'image', Auth::user()->id, $purpose, self::$userType, 'user-profile-photos');
            if($res['status'] != 200) return Utilities::error402('Sorry Photo could not be uploaded: '.$res['message']);

            return Utilities::ok(new FileResource($res['file']));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occured while trying to send verification mail, Please try again later or contact support');
        }
    }
}
