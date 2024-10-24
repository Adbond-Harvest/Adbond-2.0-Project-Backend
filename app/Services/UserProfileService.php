<?php

namespace app\Services;

use app\Notifications\APIPasswordResetNotification;
use app\Exceptions\UserNotFoundException;

use app\Models\User;
// use app\Models\Role;
// use app\Models\Staff_type;
use app\Models\Client;

// use app\Services\StaffTypeService;

use Illuminate\Support\Facades\DB;

use app\Helpers;
use app\Utilities;

/**
 * user service class
 */
class UserProfileService
{
    private $staffTypeService;

    public function __construct()
    {
        // $this->staffTypeService = new StaffTypeService;
    }

    public function update($data, $user)
    {
        if(isset($data['title'])) $user->title = $data['title'];
        if(isset($data['firstname'])) $user->firstname = $data['firstname'];
        if(isset($data['lastname'])) $user->lastname = $data['lastname'];
        // if(isset($data['email'])) $user->email = $data['email'];
        // if(isset($data['staff_type_id'])) $user->staff_type_id = $data['staff_type_id'];
        if(isset($data['phoneNumber'])) $user->phone_number = $data['phoneNumber'];
        if(isset($data['address'])) $user->address = $data['address'];
        if(isset($data['countryId'])) $user->country_id = $data['countryId'];
        if(isset($data['postalCode'])) $user->postal_code = $data['postalCode'];
        if(isset($data['maritalStatus'])) $user->marital_status = $data['maritalStatus'];
        if(isset($data['photoId'])) $user->photo_id = $data['photoId']; 
        if(isset($data['gender'])) $user->gender = $data['gender']; 
        // if(isset($data['departmentId'])) $user->department_id = $data['departmentId']; 
        // if(isset($data['positionId'])) $user->position_id = $data['positionId']; 
        if(isset($data['hybridStaffDrawId'])) $user->hybrid_staff_draw_id = $data['hybridStaffDrawId']; 
        if(isset($data['accountNumber'])) $user->account_number = $data['accountNumber'];
        if(isset($data['accountName'])) $user->account_name = $data['accountName'];
        if(isset($data['bankId'])) $user->bank_id = $data['bankId'];
        $user->update();
        return $user;
    }

    public function changePassword($password, $user)
    {
        $user->password =  bcrypt($password);
        $user->update();
    }

    public function setPassword($user, $password)
    {
        $user->password =  bcrypt($password);
        $user->password_set = true;
        $user->update();
    }

    public function changeRole($roleId, $user)
    {
        $user->role_id = $roleId;
        $user->update();
        return $user;
    }

}
