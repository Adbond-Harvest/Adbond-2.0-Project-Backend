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

use app\Models\User;

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
            $oldPhotoId = null;
            if($request->hasFile('photo')) {
                $purpose = FilePurpose::USER_PROFILE_PHOTO->value;
                if(Auth::user()->photo_id) $oldPhotoId = Auth::user()->photo_id;
                $res = $this->fileService->save($request->file('photo'), 'image', Auth::user()->id, $purpose, User::$userType, 'user-profile-photos');
                if($res['status'] != 200) return Utilities::error402('Sorry Photo could not be uploaded '.$res['message']);

                $data['photoId'] = $res['file']->id;
                $fileMeta = ["belongsId"=>Auth::user()->id, "belongsType"=>"app\Models\User"];
                $this->fileService->updateFileObj($fileMeta, $res['file']);
            }
            $user = $this->userProfileService->update($data, Auth::user());

            // delete the old photo if it exists
            if($oldPhotoId) $this->fileService->deleteFile($oldPhotoId);

            DB::commit();
            return Utilities::ok(new UserBriefResource($user));
        }catch(\Exception $e){
            DB::rollBack();
            return Utilities::error($e, 'An error occured while trying to process the request, Please try again later or contact support');
        }
    }
}

