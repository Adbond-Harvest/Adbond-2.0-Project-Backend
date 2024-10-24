<?php

namespace app\Http\Controllers\User;

use app\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use app\Http\Requests\SetPassword;
use app\Http\Requests\User\SetPassword as UserSetPassword;
use app\Http\Requests\User\UpdateProfile;
use app\Http\Resources\UserBriefResource;

use app\Services\UserProfileService;
use app\Services\FileService;

use app\Enums\FilePurpose;
use app\Utilities;

class ProfileController extends Controller
{
    private static $userType = "app\Models\User";
    private $userProfileService;
    private $fileService;

    public function __construct()
    {
        $this->userProfileService = new UserProfileService;
        $this->fileService = new FileService;
    }

    public function setPassword(UserSetPassword $request)
    {
        try{
            if(Auth::user()->password_set) return Utilities::error402("Password has already been set");
            $data = $request->validated();
            $this->userProfileService->setPassword(Auth::user(), $data['password']);
            return Utilities::okay("Password set Successfully");
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occured while trying to process the request, Please try again later or contact support');
        }
    }

    public function update(UpdateProfile $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->validated();
            if(isset($data['photoId'])) {
                $file = $this->fileService->getFile($data['photoId']);
                if(!$file) return Utilities::error402("File not found");
                if($file->purpose != FilePurpose::USER_PROFILE_PHOTO) return Utilities::error402("Sorry, you are attempting to save the wrong Photo");
                $fileMeta = ["belongsId"=>Auth::user()->id, "belongsType"=>"app\Models\User"];
            }
            $user = $this->userProfileService->update($data, Auth::user());
            if(isset($data['photoId'])) $this->fileService->updateFileObj($fileMeta, $file);

            DB::commit();
            return Utilities::ok(new UserBriefResource($user));
        }catch(\Exception $e){
            DB::rollBack();
            return Utilities::error($e, 'An error occured while trying to process the request, Please try again later or contact support');
        }
    }
}

