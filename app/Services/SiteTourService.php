<?php

namespace app\Services;

use app\Models\SiteTourSchedule;

class SiteTourService
{
    /*
        Site Tour Schedule Services
    */

    public function save($data)
    {
        $siteTourSchedule = new SiteTourSchedule;
        $siteTourSchedule->project_type_id = $data['projectTypeId'];
        $siteTourSchedule->project_id = $data['projectId'];
        $siteTourSchedule->package_id = $data['packageId'];
        $siteTourSchedule->available_date = $data['availableDate'];
        $siteTourSchedule->available_time = Carbon::createFromFormat('h:i A', $data['availableTime'])->format('H:i');

        $siteTourSchedule->save();

        return $siteTourSchedule;
    }
}