<?php

namespace app\Services;

// use app\Services\StaffTypeService;

use Illuminate\Support\Facades\DB;

use app\Models\UserActivityLog;

use app\Helpers;
use app\Utilities;

/**
 * UserActivityLog service class
 */
class UserActivityLogService
{
    public $date = null;

    public function log($user, $activity)
    {
        $activityLog = new UserActivityLog;

        $activityLog->user_id = $user->id;
        $activityLog->activity = "$user->name ".$activity;

        $activityLog->save();

        return $activityLog;
    }

    // public function getUserLogs($user)
    // {
    //     $query = UserActivityLog::query();
    //     if($date)
    // }

    public function getLogs()
    {
        return UserActivityLog::selectRaw('DATE(created_at) as date, user_id, activity, created_at, updated_at')
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('date');
    }

}
