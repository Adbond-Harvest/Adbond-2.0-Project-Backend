<?php

namespace app\Services;

use Carbon\Carbon;

use app\Models\SiteTourSchedule;

class SiteTourService
{
    /*
        Site Tour Schedule Services
    */
    public $cancelled = null;

    public function save($data)
    {
        $siteTourSchedule = new SiteTourSchedule;
        $siteTourSchedule->project_type_id = $data['projectTypeId'];
        $siteTourSchedule->project_id = $data['projectId'];
        $siteTourSchedule->package_id = $data['packageId'];
        $siteTourSchedule->fee = $data['fee'];
        $siteTourSchedule->available_date = $data['availableDate'];
        $siteTourSchedule->available_time = Carbon::createFromFormat('h:i A', $data['availableTime'])->format('H:i');

        $siteTourSchedule->save();

        return $siteTourSchedule;
    }

    public function update($data, $siteTourSchedule)
    {
        if(isset($data['projectTypeId'])) $siteTourSchedule->project_type_id = $data['projectTypeId'];
        if(isset($data['projectId'])) $siteTourSchedule->project_id = $data['projectId'];
        if(isset($data['packageId'])) $siteTourSchedule->package_id = $data['packageId'];
        if(isset($data['fee'])) $siteTourSchedule->fee = $data['fee'];
        if(isset($data['availableDate'])) $siteTourSchedule->available_date = $data['availableDate'];
        if(isset($data['availableTime'])) $siteTourSchedule->available_time = Carbon::createFromFormat('h:i A', $data['availableTime'])->format('H:i');

        $siteTourSchedule->update();

        return $siteTourSchedule;
    }

    public function delete($siteTourSchedule)
    {
        $siteTourSchedule->delete();
    }

    public function schedules($with=[])
    {
        $query = SiteTourSchedule::with($with);
        if($this->cancelled) $query->where("cancelled", $this->cancelled);
        return $query->get();
    }

    public function schedule($id, $with=[])
    {
        return SiteTourSchedule::with($with)->where("id", $id)->first();
    }
}