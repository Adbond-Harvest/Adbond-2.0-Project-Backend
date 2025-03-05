<?php

namespace app\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Http\Controllers\Controller;

use app\Http\Requests\User\CreateUser;
use app\Http\Requests\User\UpdateUser;

use app\Http\Resources\UserResource;

use app\Services\UserService;
use app\Services\UserActivityLogService;

use app\Utilities;

class StaffController extends Controller
{
    private $userService;
    private $userActivityLogService;

    public function __construct()
    {
        $this->userService = new UserService;
        $this->userActivityLogService = new UserActivityLogService;
    }

    public function save(CreateUser $request)
    {
        try{
            $data = $request->validated();
            $data['password'] = '12345';
            $user = $this->userService->save($data, Auth::user()->id);

            return Utilities::ok(new UserResource($user));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function update(UpdateUser $request, $userId)
    {
        try{
            if (!is_numeric($userId) || !ctype_digit($userId)) return Utilities::error402("Invalid parameter userId");

            $user = $this->userService->getUser($userId);
            if(!$user) return Utilities::error402("User not found");

            $data = $request->validated();
            $user = $this->userService->update($data, $user);

            return Utilities::ok(new UserResource($user));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function users()
    {
        $users = $this->userService->getUsers(Auth::user());

        return Utilities::ok(UserResource::collection($users));
    }

    public function user($userId)
    {
        if (!is_numeric($userId) || !ctype_digit($userId)) return Utilities::error402("Invalid parameter userId");

        $user = $this->userService->getUser($userId);

        return Utilities::ok(new UserResource($user));
    }

    public function activities()
    {
        $userLogs = $this->userActivityLogService->getLogs();

        return Utilities::ok($userLogs);
    }
}
