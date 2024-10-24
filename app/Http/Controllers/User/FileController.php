<?php

namespace app\Http\Controllers\User;

use app\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use app\Http\Requests\SavePhoto;

use app\Http\Resources\FileResource;

use app\Services\FileService;

use app\Enums\FilePurpose;
use app\Utilities;

class FileController extends Controller
{
    private static $userType = "appModels\User";
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
