<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use App\Http\Requests\SavePhoto;
use Illuminate\Http\Request;

use App\Http\Resources\FileResource;

use App\Services\FileService;

use App\Utilities;

class FileController extends Controller
{
    private static $userType = "App\Models\Customer";
    private $fileService;

    public function __construct()
    {
        $this->fileService = new FileService;
    }

    public function savePhoto(SavePhoto $request)
    {
        try{
            $res = $this->fileService->save($request->file('photo'), 'image', Auth::guard('customer')->user()->id, self::$userType, 'profile-photos');
            if($res['status'] != 200) return Utilities::error402('Sorry Photo could not be uploaded');

            return Utilities::okay(new FileResource($res['file']));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occured while trying to send verification mail, Please try again later or contact support');
        }
    }
}
