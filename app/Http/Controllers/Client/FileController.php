<?php

namespace app\Http\Controllers\Client;

use app\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use app\Http\Requests\SavePhoto;
use Illuminate\Http\Request;

use app\Http\Resources\FileResource;

use app\Services\FileService;

use app\Enums\FilePurpose;

use app\Utilities;

class FileController extends Controller
{
    private static $userType = "app\Models\Client";
    private $fileService;

    public function __construct()
    {
        $this->fileService = new FileService;
    }

    public function saveProfilePhoto(SavePhoto $request)
    {
        try{
            $purpose = FilePurpose::CLIENT_PROFILE_PHOTO->value;
            $res = $this->fileService->save($request->file('photo'), 'image', Auth::guard('client')->user()->id, $purpose, self::$userType, 'client-profile-photos');
            if($res['status'] != 200) return Utilities::error402('Sorry Photo could not be uploaded '.$res['message']);

            return Utilities::ok(new FileResource($res['file']));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to send verification mail, Please try again later or contact support');
        }
    }

    public function savePaymentEvidence(SavePhoto $request)
    {
        try{
            $purpose = FilePurpose::PAYMENT_EVIDENCE->value;
            $res = $this->fileService->save($request->file('photo'), 'image', Auth::guard('client')->user()->id, $purpose, self::$userType, 'payment-evidences');
            if($res['status'] != 200) return Utilities::error402('Sorry Photo could not be uploaded '.$res['message']);

            return Utilities::ok(new FileResource($res['file']));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to send verification mail, Please try again later or contact support');
        }
    }
}
