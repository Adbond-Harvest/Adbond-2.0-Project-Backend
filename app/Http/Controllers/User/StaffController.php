<?php

namespace app\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Http\Controllers\Controller;

use app\Http\Requests\User\CreateUser;

use app\Http\Resources\UserResource;

use app\Services\UserService;

use app\Utilities;

class StaffController extends Controller
{
    private $userService;

    public function __construct()
    {
        $this->userService = new UserService;
    }

    public function save(CreateUser $request)
    {
        try{
            $data = $request->validated();
            $user = $this->userService->save($data, Auth::user()->id);

            return Utilities::ok(new UserResource($user));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function users()
    {
        $users = $this->userService->getUsers();

        return Utilities::ok(UserResource::collection($users));
    }
}
