<?php

namespace App\Services;

use App\Exceptions\UserNotFoundException;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

use App\Services\AgeGroupService;

use App\Models\Client;
use App\Models\ClientNextOfKin;

use App\Enums\KYCStatus;
use App\Helpers;

/**
 * client service class
 */
class ClientService
{

    public function getClient($id)
    {
        return Client::find($id);
    }

    public function getClientByEmail($email)
    {
        return Client::where('email', $email)->first();
    }

    public function getClientByProvider($provider_name, $provider_id)
    {
        return Client::where('provider_name', $provider_name)->where('provider_id', $provider_id)->first();
    }

    public function getClients($page=1, $perPage=10)
    {
        if($page <= 0) $page = 1;
        $offset = $perPage * ($page-1);
        return Client::limit($perPage)->offset($offset)->orderBy('created_at', 'DESC')->get();
    }

    public function searchClients($string)
    {
        return Client::where('firstname', 'like', '%'.$string.'%')->orWhere('lastname', 'like', '%'.$string.'%')->get();
    }

    public function totalClients()
    {
        return Client::count();
    }

    public function save($data)
    {
        $client = new Client;
        if(isset($data['title'])) $client->title = $data['title'];
        $client->firstname = $data['firstname'];
        $client->lastname = $data['lastname'];
        $client->email = $data['email'];
        $client->password =  bcrypt($data['password']);
        $client->email_verified_at = $data['emailVerifiedAt'];
        if(isset($data['othernames'])) $client->othernames = $data['othernames'];
        if(isset($data['photoId'])) $client->photo_id = $data['photoId'];
        if(isset($data['gender'])) $client->gender = $data['gender'];
        if(isset($data['phoneNumber'])) $client->phone_number = $data['phoneNumber'];
        if(isset($data['address'])) $client->address = $data['address'];
        if(isset($data['countryId'])) $client->country_id = $data['countryId'];
        if(isset($data['stateId'])) $client->state_id = $data['stateId'];
        if(isset($data['ageGroupId'])) $client->age_group_id = $data['ageGroupId'];
        if(isset($data['refererId'])) $client->referer_id = $data['refererId'];
        if(isset($data['marital_status'])) $client->marital_status = $data['maritalStatus'];
        if(isset($data['employment_status'])) $client->employment_status = $data['employmentStatus'];
        if(isset($data['occupation'])) $client->occupation = $data['occupation'];
        if(isset($data['postalCode'])) $client->postal_code = $data['postalCode'];
        $client->save();
        return $client;
    }

    public function update($data, $client)
    {
        if(isset($data['title'])) $client->title = $data['title'];
        if(isset($data['firstname'])) $client->firstname = $data['firstname'];
        if(isset($data['lastname'])) $client->lastname = $data['lastname'];
        // if(isset($data['email'])) $client->email = $data['email'];
        if(isset($data['photoId'])) $client->photo_id = $data['photoId'];
        if(isset($data['gender'])) $client->gender = $data['gender'];
        if(isset($data['phoneNumber'])) $client->phone_number = $data['phone_number'];
        if(isset($data['address'])) $client->address = $data['address'];
        if(isset($data['countryId'])) $client->country_id = $data['countryId'];
        if(isset($data['stateId'])) $client->state_id = $data['stateId'];
        // if(isset($data['referer_id'])) $client->referer_id = $data['referer_id'];
        if(isset($data['maritalStatus'])) $client->marital_status = $data['maritalStatus'];
        if(isset($data['employmentStatus'])) $client->employment_status = $data['employmentStatus'];
        if(isset($data['occupation'])) $client->occupation = $data['occupation'];
        // /if(array_key_exists('occupation', $data));
        if(isset($data['postalCode'])) $client->postal_code = $data['postalCode'];
        if(isset($data['ageGroupId'])) $client->age_group_id = $data['ageGroupId'];
        if(isset($data['dob'])) {
            $ageGroupService = new AgeGroupService;
            $ageGroup = $ageGroupService->getGroupFromDob($data['dob']);
            if($ageGroup) $client->age_group_id = $ageGroup->id;
            $client->dob = $data['dob'];
        }
        $client->update();

        //Check and update the kyc status
        if($client->kyc_status == KYCStatus::NOTSTARTED->value || $client->kyc_status == KYCStatus::STARTED->value) {
            if(Helpers::kycCompleted($client)) {
                $client->kyc_status = KYCStatus::COMPLETED->value;
                $client->update();
            }elseif(Helpers::kycStarted($client)) {
                $client->kyc_status = KYCStatus::STARTED->value;
                $client->update();
            }
        }
        return $client;
    }

    public function saveGoogleUser($data)
    {
        $client = new Client;
        $name = explode(' ', $data['name']);
        $client->firstname = $name[0];
        if(isset($name[1])) $client->lastname = $name[1];
        $client->email = $data['email'];
        $client->provider_id = $data['provider_id'];
        $client->provider_name = $data['provider_name'];
        $client->activated = 1;
        $client->email_verified_at = Carbon::now();
        $client->save();
        return $client;
    }

    public function delete($client)
    {
        $client->delete();
    }



    public function updateNextOfKin($data)
    {
        $kin = (isset($data['kin'])) ? $data['kin'] : new ClientNextOfKin;
        $kin->client_id = $data['clientId'];
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