<?php

namespace App\Services;

use App\Notifications\APIPasswordResetNotification;
use App\Exceptions\UserNotFoundException;

use App\Models\AgeGroup;

use App\Helpers;

/**
 * AgeGroup service class
 */
class AgeGroupService
{

    public function getGroups()
    {
        return AgeGroup::all();
    }

    public function getGroup($id)
    {
        return AgeGroup::find($id);
    }

    public function getGroupFromAge($age)
    {
        return AgeGroup::where('start', '<=', $age)->where('end', '>=', $age)->first();
    } 
    
    public function getGroupFromDob($dob)
    {
        $age = Helpers::getAge($dob);
        return $this->getGroupFromAge($age);
    }

    public function save($data)
    {
        $ageGroup = new AgeGroup;
        $ageGroup->start = $data['start'];
        $ageGroup->end = $data['end'];
        $ageGroup->save();
        return $ageGroup;
    }

    public function update($data, $ageGroup)
    {
        if(isset($data['start'])) $ageGroup->start = $data['start'];
        if(isset($data['end'])) $ageGroup->end = $data['end'];
        $ageGroup->update();
        return $ageGroup;
    }

}
