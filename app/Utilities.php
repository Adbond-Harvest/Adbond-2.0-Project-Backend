<?php

namespace App;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\Services\AuthService;
use App\Models\User;


class Utilities
{
    public $guard;
    public $reference;

    public function __construct($guard, $reference)
    {
        $this->guard = $guard;
        $this->reference = $reference;
    }

    public static function error($e, $message='')
    {
        Log::stack(['project'])->info($e->getMessage().' in '.$e->getFile().' at Line '.$e->getLine());
        return response()->json([
            'statusCode' => 500,
            'message' => ($message != '') ? $message : 'An error occured while trying to perform this operation, Please try again later or contact support'
        ], 500);
    }

    public static function customError($message, $statusCode)
    {
        return response()->json([
            'statusCode' => $statusCode,
            'message' => $message
        ], $statusCode);
    }

    public static function logStuff($message)
    {
        Log::stack(['project'])->info($message);
    }

    public function refreshToken()
    {
        $authService = new AuthService($this->guard, $this->reference);
        return $authService->checkToRefreshToken();
    }

    public static function ok($data)
    {
        return response()->json([
            'statusCode' => 200,
            'data' => $data
        ], 200);
    }

    public static function okay($message='', $data=null, )
    {
        $responseData = ['statusCode' => 200];
        if(!empty($data) || $data != '') $responseData['data'] = $data;
        return response()->json([
            'statusCode' => 200,
            'data' => $data,
            'message' => $message
        ], 200);
    }

    public static function custom($statusCode, $res=[])
    {
        $responseData = ['statusCode' => $statusCode];
        $message = (isset($res['message'])) ? $res['message'] : '';
        $data = (isset($res['data'])) ? $res['data'] : '';
        if(!empty($data) || $data != '') $responseData['data'] = $data;
        return response()->json([
            'statusCode' => $statusCode,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    public function paginatedOk($data, $page, $perPage, $total)
    {
        $responseData = ['statusCode' => 200];
        if(!empty($data) || $data != '') $responseData['data'] = $data;
        return response()->json([
            'statusCode' => 200,
            'data' => $data,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            // 'token' => Utilities::refreshToken($this->guard)
        ], 200);
    }

    public static function paginatedOkay($data, $page, $perPage, $total)
    {
        $responseData = ['statusCode' => 200];
        if(!empty($data) || $data != '') $responseData['data'] = $data;
        return response()->json([
            'statusCode' => 200,
            'data' => $data,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            // 'token' => Utilities::refreshToken($this->guard)
        ], 200);
    }

    public static function ok2($data='')
    {
        $responseData = ['statusCode' => 200];
        if(!empty($data) || $data != '') $responseData['data'] = $data;
        return response()->json([
            'statusCode' => 200,
            'data' => $data,
            'token' => ''
        ], 200);
    }

    public static function error402($message)
    {
        return response()->json([
            'statusCode' => 402,
            'message' => $message
        ], 402);
    }


    public static function isMidYear($date)
    {
        $date = strtotime($date);
        $month = date('m', $date);
        // $date = date('Y-m-d');
        $monthWeek = self::weekOfMonth($date);
        // dd($month);
        return ( (($month==6 || $month==06) && ($monthWeek==3 || $monthWeek==4 || $monthWeek==5))  ||  (($month==7 || $month==07) && ($monthWeek==1)) ) ? true : false;
    }

    public static function isEndYear($date)
    {
        $date = strtotime($date);
        $month = date('m', $date);
        // $date = date('Y-m-d');
        $monthWeek = self::weekOfMonth($date);
        return ( (($month==12 || $month==11) && ($monthWeek==3 || $monthWeek==4 || $monthWeek==5))  ||  (($month==1 || $month==01) && ($monthWeek==1)) ) ? true : false;
    }

    public static function weekOfMonth($date) {
        //Get the first day of the month.
        $firstOfMonth = strtotime(date("Y-m-01", $date));
        //Apply above formula.
        return self::weekOfYear($date) - self::weekOfYear($firstOfMonth) + 1;
    }
    
    private static function weekOfYear($date) {
        $weekOfYear = intval(date("W", $date));
        if (date('n', $date) == "1" && $weekOfYear > 51) {
            // It's the last week of the previos year.
            return 0;
        }
        else if (date('n', $date) == "12" && $weekOfYear == 1) {
            // It's the first week of the next year.
            return 53;
        }
        else {
            // It's a "normal" week.
            return $weekOfYear;
        }
    }

    /*
    * Gets the total value, from the percentage of the total value and the discount gotten by subtracting the percentage value from the total value
    i.e where, total value = 100 and percentage = 10% thus discount = 90. if only percentage and discount is given, the function calculates the initial total value 
    */
    public static function getTotalFromDiscountAndPercentage($percentage, $discount)
    {
        return (float)((float)$discount/(1-($percentage/100)));
    }

    public static function generateRandomString($length)
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        do{
            $code = substr(str_shuffle($permitted_chars), 0, 5);
            $exists = User::where('referer_code', $code)->first();
        }while($exists);
        return $code;
    }

    public static function convertTo12HrsTimeFormat($time)
    {
        $timeArr = explode(':', $time);
        $hr = $timeArr[0];
        $newHr = $hr;
        $part = 'AM';
        if((int)$hr > 12) {
            $newHr = (int)$hr - 12;
        }
        if((int)$hr > 11 && (int)$hr < 24) {
            $part = 'PM';
        }
        return $newHr.':'.$timeArr[1].' '.$part;
    }

    public static function convertTo24HrsTimeFormat($time)
    {
        $timeStringArr = explode(' ', $time);
        $timeString = $timeStringArr[0];
        $part = $timeStringArr[1];

        $timeArr = explode(':', $timeString);
        $hr = $timeArr[0];
        $newHr = $hr;
        if(strtoupper($part) == 'PM' && (int)$hr < 12) {
            $newHr = (int)$hr + 12;
        }
        if(strtoupper($part) == 'AM' && (int)$hr == 12) {
            $newHr = '00';
        }
        return $newHr.':'.$timeArr[1];
    }

    /*
    *   Gets the difference between two dates
    */
    public static function dateDiff($end=null, $start=null)
    {
        $date1 = ($start==null) ? new \DateTime() : new \DateTime($start);
        $date2 = ($end==null) ? new \DateTime() : new \DateTime($end);
        // $diff = now()->diffInDays($model->created_at);
        return $date1->diff($date2);
    }

    public static function getDates($weekPosition, $day)
    {
        $dates = [];
        $tz = date_default_timezone_get();
        date_default_timezone_set('Africa/Lagos');
        $dayName = substr($day, 0, 3);
        
        switch($weekPosition) {
            case 1:
                $times[] = strtotime('first '.$dayName.' of this month');
                $times[] = strtotime('first '.$dayName.' of next month');
                $dates = Self::returnFormattedFutureDates($times);
                break;
            case 2:
                $times[] = strtotime('first '.$dayName.' of this month +1 week');
                $times[] = strtotime('first '.$dayName.' of next month +1 week');
                $dates = Self::returnFormattedFutureDates($times);
                break;
            case 3:
                $times[] = strtotime('first '.$dayName.' of this month +2 week');
                $times[] = strtotime('first '.$dayName.' of next month +2 week');
                $dates = Self::returnFormattedFutureDates($times);
                break;
            case 4:
                $time = strtotime('first '.$dayName.' of this month +3 week');
                $month = date('n', $time);
                $currMonth = date('n', strtotime("now"));
                if($month == $currMonth) $times[] = $time;

                $time = strtotime('first '.$dayName.' of next month +3 week');
                $month = date('n', $time);
                $currMonth = date('n', strtotime("now"));
                if($month == ($currMonth+1)) $times[] = $time;
                //dd($times);
                $dates = Self::returnFormattedFutureDates($times);
                //dd($dates);
                break;
            case 5:
                $times[] = strtotime('last '.$dayName.' of this month');
                $times[] = strtotime('last '.$dayName.' of next month');
                $dates = Self::returnFormattedFutureDates($times);
                break;
        }
        return $dates;
    }

    private static function returnFormattedFutureDates($times)
    {
        $dates = [];
        $now = strtotime("now"); 
        foreach($times as $time) {
            if($time > $now) {
                $dates[] = date('F j, Y', $time);
            }
        }
        return $dates;
    }

}