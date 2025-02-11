<?php

namespace app\Services;

use Carbon\Carbon;

use app\Models\SiteTourSchedule;
use app\Models\SiteTourBooking;

class SiteTourService
{
    /*
        Site Tour Schedule Services
    */
    public $cancelled = null;
    public $future = null;
    public $filter = null;

    public function save($data)
    {
        $siteTourSchedule = new SiteTourSchedule;
        $siteTourSchedule->project_type_id = $data['projectTypeId'];
        $siteTourSchedule->project_id = $data['projectId'];
        $siteTourSchedule->package_id = $data['packageId'];
        $siteTourSchedule->fee = $data['fee'];
        $siteTourSchedule->available_date = $data['availableDate'];
        $siteTourSchedule->available_time = Carbon::createFromFormat('h:i A', $data['availableTime'])->format('H:i:s');

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
        if(isset($data['availableTime'])) $siteTourSchedule->available_time = Carbon::createFromFormat('h:i A', $data['availableTime'])->format('H:i:s');

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
        if($this->future) $query->where("available_date", ">", Carbon::now());
        if($this->filter) {
            $filter = $this->filter;
            if(isset($filter['projectTypeId'])) $query->where("project_type_id", $filter['projectTypeId']);
            if(isset($filter['projectId'])) $query->where("project_id", $filter['projectId']);
            if(isset($filter['packageId'])) $query->where("package_id", $filter['packageId']);
            if(isset($filter['date'])) $query = $query->whereDate("available_date", $filter['date']);
            if(isset($filter['text'])) {
                $query->whereHas('package', function($packageQuery) use($filter) {
                    $packageQuery->where("name", "LIKE", "%".$filter['text']."%");
                })->whereHas('project', function($projectQuery) use($filter) {
                    $projectQuery->where("name", "LIKE", "%".$filter['text']."%");
                })->whereHas('projectType', function($projectTypeQuery) use($filter) {
                    $projectTypeQuery->where("name", "LIKE", "%".$filter['text']."%");
                });
            }
        }
        if($this->cancelled) $query->where("cancelled", $this->cancelled);
        return $query->orderBy("available_date", "DESC")->get();
    }

    public function schedule($id, $with=[])
    {
        return SiteTourSchedule::with($with)->where("id", $id)->first();
    }


    /*
        Site Tour Bookings
    */

    public function book($schedule, $clientId)
    {
        $booking = new SiteTourBooking;

        $booking->client_id = $clientId;
        $booking->site_tour_schedule_id = $schedule->id;
        $booking->save();

        return $booking;
    }

    public function cancelBooking($booking)
    {
        $booking->delete();
    }

    public function clientBookings($clientId)
    {
        return SiteTourBooking::with("schedule")->where("client_id", $clientId)->get();
    }
}