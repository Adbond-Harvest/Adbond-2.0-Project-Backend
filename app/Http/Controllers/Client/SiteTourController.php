<?php

namespace app\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use app\Http\Controllers\Controller;

use app\Http\Requests\Client\BookSiteTour;

use app\Http\Resources\SiteTourBookingResource;

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

            $booking = $this->siteTourService->book($schedule, Auth::guard('client')->user()->id);

            return Utilities::ok(new SiteTourBookingResource($booking));
        }catch (\Exception $e) {
            return Utilities::error($e, 'An error occurred while attempting to carry out this operation');
        }
    }

    public function schedules(Request $request)
    {
        
    }
}
