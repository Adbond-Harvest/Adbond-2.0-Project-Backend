<?php

namespace App\Services;

use App\Exceptions\UserNotFoundException;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

use App\Services\AgeGroupService;

use App\Models\Customer;
use App\Models\CustomerNextOfKin;

use App\Enums\KYCStatus;
use App\Helpers;

/**
 * customer service class
 */
class CustomerService
{

    public function getCustomer($id)
    {
        return Customer::find($id);
    }

    public function getCustomerByEmail($email)
    {
        return Customer::where('email', $email)->first();
    }

    public function getCustomerByProvider($provider_name, $provider_id)
    {
        return Customer::where('provider_name', $provider_name)->where('provider_id', $provider_id)->first();
    }

    public function getCustomers($page=1, $perPage=10)
    {
        if($page <= 0) $page = 1;
        $offset = $perPage * ($page-1);
        return Customer::limit($perPage)->offset($offset)->orderBy('created_at', 'DESC')->get();
    }

    public function searchCustomers($string)
    {
        return Customer::where('firstname', 'like', '%'.$string.'%')->orWhere('lastname', 'like', '%'.$string.'%')->get();
    }

    public function totalCustomers()
    {
        return Customer::count();
    }

    public function save($data)
    {
        $customer = new Customer;
        if(isset($data['title'])) $customer->title = $data['title'];
        $customer->firstname = $data['firstname'];
        $customer->lastname = $data['lastname'];
        $customer->email = $data['email'];
        $customer->password =  bcrypt($data['password']);
        $customer->email_verified_at = $data['emailVerifiedAt'];
        if(isset($data['othernames'])) $customer->othernames = $data['othernames'];
        if(isset($data['photoId'])) $customer->photo_id = $data['photoId'];
        if(isset($data['gender'])) $customer->gender = $data['gender'];
        if(isset($data['phoneNumber'])) $customer->phone_number = $data['phoneNumber'];
        if(isset($data['address'])) $customer->address = $data['address'];
        if(isset($data['countryId'])) $customer->country_id = $data['countryId'];
        if(isset($data['stateId'])) $customer->state_id = $data['stateId'];
        if(isset($data['ageGroupId'])) $customer->age_group_id = $data['ageGroupId'];
        if(isset($data['refererId'])) $customer->referer_id = $data['refererId'];
        if(isset($data['marital_status'])) $customer->marital_status = $data['maritalStatus'];
        if(isset($data['employment_status'])) $customer->employment_status = $data['employmentStatus'];
        if(isset($data['occupation'])) $customer->occupation = $data['occupation'];
        if(isset($data['postalCode'])) $customer->postal_code = $data['postalCode'];
        $customer->save();
        return $customer;
    }

    public function update($data, $customer)
    {
        if(isset($data['title'])) $customer->title = $data['title'];
        if(isset($data['firstname'])) $customer->firstname = $data['firstname'];
        if(isset($data['lastname'])) $customer->lastname = $data['lastname'];
        // if(isset($data['email'])) $customer->email = $data['email'];
        if(isset($data['photoId'])) $customer->photo_id = $data['photoId'];
        if(isset($data['gender'])) $customer->gender = $data['gender'];
        if(isset($data['phoneNumber'])) $customer->phone_number = $data['phone_number'];
        if(isset($data['address'])) $customer->address = $data['address'];
        if(isset($data['countryId'])) $customer->country_id = $data['countryId'];
        if(isset($data['stateId'])) $customer->state_id = $data['stateId'];
        // if(isset($data['referer_id'])) $customer->referer_id = $data['referer_id'];
        if(isset($data['maritalStatus'])) $customer->marital_status = $data['maritalStatus'];
        if(isset($data['employmentStatus'])) $customer->employment_status = $data['employmentStatus'];
        if(isset($data['occupation'])) $customer->occupation = $data['occupation'];
        // /if(array_key_exists('occupation', $data));
        if(isset($data['postalCode'])) $customer->postal_code = $data['postalCode'];
        if(isset($data['ageGroupId'])) $customer->age_group_id = $data['ageGroupId'];
        if(isset($data['dob'])) {
            $ageGroupService = new AgeGroupService;
            $ageGroup = $ageGroupService->getGroupFromDob($data['dob']);
            if($ageGroup) $customer->age_group_id = $ageGroup->id;
            $customer->dob = $data['dob'];
        }
        $customer->update();

        //Check and update the kyc status
        if($customer->kyc_status == KYCStatus::NOTSTARTED->value || $customer->kyc_status == KYCStatus::STARTED->value) {
            if(Helpers::kycCompleted($customer)) {
                $customer->kyc_status = KYCStatus::COMPLETED->value;
                $customer->update();
            }elseif(Helpers::kycStarted($customer)) {
                $customer->kyc_status = KYCStatus::STARTED->value;
                $customer->update();
            }
        }
        return $customer;
    }

    public function saveGoogleUser($data)
    {
        $customer = new Customer;
        $name = explode(' ', $data['name']);
        $customer->firstname = $name[0];
        if(isset($name[1])) $customer->lastname = $name[1];
        $customer->email = $data['email'];
        $customer->provider_id = $data['provider_id'];
        $customer->provider_name = $data['provider_name'];
        $customer->activated = 1;
        $customer->email_verified_at = Carbon::now();
        $customer->save();
        return $customer;
    }

    public function delete($customer)
    {
        $customer->delete();
    }



    public function updateNextOfKin($data)
    {
        $kin = (isset($data['kin'])) ? $data['kin'] : new CustomerNextOfKin;
        $kin->customer_id = $data['customerId'];
        if(isset($data['title'])) $kin->title = $data['title'];
        $kin->firstname = $data['firstname'];
        $kin->lastname = $data['lastname'];
        $kin->phone_number = $data['phoneNumber'];
        $kin->relationship = $data['relationship'];
        if(isset($data['gender'])) $kin->gender = $data['gender'];
        if(isset($data['email'])) $kin->email = $data['email'];
        if(isset($data['address'])) $kin->address = $data['address'];
        if(isset($data['countryId'])) $kin->country_id = $data['countryId'];
        if(isset($data['stateId'])) $kin->state_id = $data['stateId'];
        $kin->save();
        return $kin;
    }

}