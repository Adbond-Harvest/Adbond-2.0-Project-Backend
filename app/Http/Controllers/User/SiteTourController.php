<?php

namespace app\Http\Controllers\User;

use Illuminate\Http\Request;
use app\Http\Controllers\Controller;

use app\Http\Requests\User\CreateSiteTourSchedule;

use app\Http\Resources\SiteTourScheduleResource;

use app\Services\SiteTourService;

class SiteTourController extends Controller
{
    private $siteTourService;

    public function __construct()
    {
        $this->siteTourService = new SiteTourService;
    }
}
