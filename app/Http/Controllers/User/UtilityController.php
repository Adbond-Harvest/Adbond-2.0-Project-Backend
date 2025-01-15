<?php

namespace app\Http\Controllers\User;

use Illuminate\Http\Request;
use app\Http\Controllers\Controller;

use app\Http\Resources\StaffTypeResource;
use app\Http\Resources\RoleResource;

use app\Services\StaffTypeService;
use app\Services\RoleService;

use app\Utilities;

class UtilityController extends Controller
{
    private $staffTypeServices;
    private $roleService;

    public function __construct()
    {
        $this->staffTypeServices = new StaffTypeService;
        $this->roleService = new RoleService;
    }

    public function staffTypes()
    {
        $staffTypes = $this->staffTypeServices->getStaffTypes();

        return Utilities::ok(StaffTypeResource::collection($staffTypes));
    }

    public function roles()
    {
        $roles = $this->roleService->roles();
    }
    
}
