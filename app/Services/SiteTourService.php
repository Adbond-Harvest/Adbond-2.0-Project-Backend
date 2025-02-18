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
    public $count = null;
    public $cancelled = null;
    public $future = null;
    public $filter = null;
    public $clientId = null;
    public $booked = null;

    public function save($data)
    {
        $siteTourSchedule = new SiteTourSchedule;
        $siteTourSchedule->project_type_id = $data['projectTypeId'];
        $siteTourSchedule->project_id = $data['projectId'];
        $siteTourSchedule->package_id = $data['packageId'];
        $siteTourSchedule->fee = $data['fee'];
        $siteTourSchedule->slots = $data['slots'];
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
        if(isset($data['slots'])) $siteTourSchedule->slots = $data['slots'];
        if(isset($data['availableDate'])) $siteTourSchedule->available_date = $data['availableDate'];
        if(isset($data['availableTime'])) $siteTourSchedule->available_time = Carbon::createFromFormat('h:i A', $data['availableTime'])->format('H:i:s');

        $siteTourSchedule->update();

        return $siteTourSchedule;
    }

    public function delete($siteTourSchedule)
    {
        $siteTourSchedule->delete();
    }

    public function schedules($with=[], $offset=0, $perPage=null)
    {
        $query = SiteTourSchedule::with($with);
        if($this->booked === true) {
            // if we are getting schedules that are booked
            if($this->clientId) {
                // if the booking we are looking for is for a particular client
                $query->whereHas("clients", function($clientQuery) {
                    $clientQuery->where("client_id", $this->clientId);
                });
            }else{
                // if its not for a particular client
                $query->whereHas("clients");
            }
        }
        if($this->booked === false) {
            // if we are getting schedules that are not booked
            if($this->clientId) {
                // if we are looking for schedules not booked by a particular client
                $query->whereDoesntHave("clients", function($clientQuery) {
                    $clientQuery->where("client_id", $this->clientId);
                });
            }else{
                // if its not for a particular client
                $query->whereDoesntHave("clients");
            }
        }
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

        if($this->count) return $query->count();
        return $query->orderBy("created_at", "DESC")->offset($offset)->limit($perPage)->get();
        // return $query->orderBy("available_date", "DESC")->get();
    }

    // public function unbookedSchedules($with=[])
    // {
    //     SiteTourSchedule::whereDoesntHave('clients', function ($query) {
    //         $query->where('client_id', $clientId);
    //     });
    // }

    public function schedule($id, $with=[])
    {
        return SiteTourSchedule::with($with)->where("id", $id)->first();
    }

    public function deductSlots($siteTourSchedule, $slots = 1)
    {
        $remainingSlots = $siteTourSchedule->slots - $slots;
        $siteTourSchedule->slots = ($remainingSlots >= 0) ? $remainingSlots : 0;

        $siteTourSchedule->update();

        return $siteTourSchedule;
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