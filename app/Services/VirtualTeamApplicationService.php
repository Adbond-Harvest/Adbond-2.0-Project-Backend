<?php

namespace app\Services;

use app\Models\VirtualTeamApplication;

class VirtualTeamApplicationService
{
    public $count = null;

    public function save($data)
    {
        $application = new VirtualTeamApplication;

        $application->firstname = $data['firstname'];
        $application->lastname = $data['lastname'];
        $application->email = $data['email'];
        $application->location = $data['location'];
        $application->reason = $data['reason'];

        $application->save();

        return $application;
    }

    public function update($data, $application)
    {
        if(isset($data['firstname'])) $application->firstname = $data['firstname'];
        if(isset($data['lastname'])) $application->lastname = $data['lastname'];
        if(isset($data['email'])) $application->email = $data['email'];
        if(isset($data['location'])) $application->location = $data['location'];
        if(isset($data['reason'])) $application->reason = $data['reason'];

        $application->update();

        return $application;
    }

    public function applications($offset=0, $perPage=null)
    {
        $query = VirtualTeamApplication::where("converted", false);
        if($this->count) return $query->count();

        if($perPage==null) $perPage=env('PAGINATION_PER_PAGE');
        return $query->offset($offset)->limit($perPage)->orderBy("created_at", "DESC")->get();
    }

    public function application($applicationId)
    {
        return VirtualTeamApplication::find($applicationId);
    }
}