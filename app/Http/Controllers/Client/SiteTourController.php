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

            if($schedule->cancelled == 1) return Utilities::error402("This site Tour has been cancelled");
            if (Carbon::parse($schedule->available_date)->isPast())  return Utilities::error402("This site Tour has passed");

            if($schedule->slots == 0) return Utilities::error402("Sorry.. There are no more slots available for this Site Tour");

            $booking = $this->siteTourService->book($schedule, Auth::guard('client')->user()->id);
            $this->siteTourService->deductSlots($schedule);

            return Utilities::ok(new SiteTourBookingResource($booking));
        }catch (\Exception $e) {
            return Utilities::error($e, 'An error occurred while attempting to carry out this operation');
        }
    }

    public function schedules(Request $request)
    {
        $schedules = Auth::guard('client')->user()->siteTourSchedules;

        return Utilities::ok(SiteTourScheduleResource::collection($schedules));
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
