<?php

namespace app\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use app\Http\Controllers\Controller;

use app\Http\Requests\Client\BookSiteTour;

use app\Http\Resources\SiteTourBookingResource;
use app\Http\Resources\SiteTourScheduleResource;

use app\Services\SiteTourService;

use app\Enums\Weekday;

use app\Utilities;

class SiteTourController extends Controller
{
    private $siteTourService;

    public function __construct()
    {
        $this->siteTourService = new SiteTourService;
    }

    public function book(BookSiteTour $request)
    {
        try{
            $schedule = $this->siteTourService->schedule($request->validated("siteTourScheduleId"));
            if(!$schedule) return Utilities::error402("Site Tour Schedule not found");

            $bookedSchedule = $this->siteTourService->getBookedSchedule($schedule->id, $request->validated("bookedDate"));

            if($bookedSchedule) {
                if($bookedSchedule->cancelled == 1) return Utilities::error402("This site Tour schedule has been cancelled");
                if($bookedSchedule->total >= $schedule->slots) return Utilities::error402("This Schedule is fully booked");
            }

            $bookedSchedule = $this->siteTourService->saveBookedSchedule($schedule->id, $request->validated("bookedDate"), $bookedSchedule);

            $data['clientId'] = Auth::guard('client')->user()->id;
            $data['firstname'] = Auth::guard('client')->user()->firstname;
            $data['lastname'] = Auth::guard('client')->user()->lastname;
            $data['email'] = Auth::guard('client')->user()->email;
            if(Auth::guard('client')->user()->phone_number) $data['phoneNumber'] = Auth::guard('client')->user()->phone_number;

            $booking = $this->siteTourService->book($bookedSchedule, $data);

            return Utilities::okay("Site Tour Booked Successfully");
        }catch (\Exception $e) {
            return Utilities::error($e, 'An error occurred while attempting to carry out this operation');
        }
    }

    public function schedules(Request $request)
    {
        $schedules = Auth::guard('client')->user()->siteTourSchedules;

        return Utilities::ok(SiteTourScheduleResource::collection($schedules));
    }

    public function filterSchedules(Request $request)
    {
        $filter = [];
        if($request->query('projectTypeId')) $filter['projectTypeId'] = $request->query('projectTypeId');
        if($request->query('projectId')) $filter['projectId'] = $request->query('projectId');
        if($request->query('packageId')) $filter['packageId'] = $request->query('packageId');
        if($request->query('date')) $filter['date'] = $request->query('date');
        if($request->query('time')) $filter['time'] = $request->query('time');

        $this->siteTourService->filter = $filter;
        $this->siteTourService->future = true;

        $schedules = $this->siteTourService->schedules();

        $result = [];

        if(isset($filter['projectTypeId']) && !isset($filter['projectId'])) {
            if($schedules->count() > 0) {
                foreach($schedules as $schedule) $result[$schedule->project->id] = $schedule->project;
            }
            return Utilities::ok($result);
        }

        if(isset($filter['projectId']) && !isset($filter['packageId'])) {
            if($schedules->count() > 0) {
                foreach($schedules as $schedule) $result[$schedule->package_id] = $schedule->package;
            }
            return Utilities::ok($result);
        }

        if(!isset($filter['date']) && isset($filter['packageId'])) {
            $dates = ($schedules->count() > 0) ? $this->getScheduleDates($schedules) : [];
            return Utilities::ok($dates);
        }
        if(isset($filter['date'])) {
            $scheduleTimes = [];
            if($schedules->count() > 0) {
                $recurrentTime = [];
                // make sure that recurrent time takes precedence over non-recurrent time
                foreach($schedules as $schedule) {
                    if($schedule->recurrent == 1) {
                        $scheduleTimes[$schedule->id] = $schedule->available_time;
                        $recurrentTime[] = $schedule->available_time;
                    }
                }
                foreach($schedules as $schedule) {
                    if($schedule->recurrent == 0) {
                        if(!in_array($schedule->available_time, $recurrentTime)) $scheduleTimes[$schedule->id] = $schedule->available_time;
                    }
                }
            }
            return Utilities::ok($scheduleTimes);
        }
        return Utilities::ok(SiteTourScheduleResource::collection($schedules));
    }

    private function getScheduleDates($schedules)
    {
        // dd('this');
        $dates = [];
        // dd($schedules);
        foreach($schedules as $schedule) {
            if($schedule->recurrent == 1) {
                if($schedule->recurrent_day == Weekday::ALL->value) {
                    $monthDates = Utilities::getDatesForAMonth();
                    foreach($monthDates as $date) if(!in_array($date, $dates)) $dates[] = $date;
                    break;
                }else{
                    $monthDates = Utilities::getMonthDatesForWeekday($schedule->recurrent_day);
                    foreach($monthDates as $date) if(!in_array($date, $dates)) $dates[] = $date;
                }
            }else{
                if(!in_array($schedule->available_date, $dates)) $dates[] = $schedule->available_date;
            }
        }
        return $dates;
    }

    public function siteTours(Request $request)
    {
        $this->siteTourService->booked = false;
        $this->siteTourService->clientId = Auth::guard('client')->user()->id;
        $this->siteTourService->future = true;

        $page = ($request->query('page')) ?? 1;
        $perPage = ($request->query('perPage'));
        if(!is_int((int) $page) || $page <= 0) $page = 1;
        if(!is_int((int) $perPage) || $perPage==null) $perPage = env('PAGINATION_PER_PAGE');
        $offset = $perPage * ($page-1);

        $filter = [];
        if($request->query('text')) $filter["text"] = $request->query('text');
        if($request->query('date')) $filter["date"] = $request->query('date');

        $schedules = $this->siteTourService->schedules([], $offset, $perPage);

        $this->siteTourService->count = true;
        $schedulesCount = $this->siteTourService->schedules();

        return Utilities::paginatedOkay(SiteTourScheduleResource::collection($schedules), $page, $perPage, $schedulesCount);
    }
}
