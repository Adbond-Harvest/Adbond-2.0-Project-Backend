<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\SetPassword;
use App\Http\Requests\User\SetPassword as UserSetPassword;
use App\Http\Requests\User\UpdateProfile;
use App\Http\Resources\UserBriefResource;

use App\Services\UserProfileService;
use App\Services\FileService;

use App\Utilities;

class ProfileController extends Controller
{
    private static $userType = "App\Models\User";
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
            $data = $request->validated();
            $file = $this->fileService->getFile($data['photoId']);
            if($file->user_type != Self::$userType) return Utilities::error402("Sorry, you are attempting to save the wrong Photo");
            $user = $this->userProfileService->update($data, Auth::user());
            return Utilities::ok(new UserBriefResource($user));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occured while trying to process the request, Please try again later or contact support');
        }
    }
}

