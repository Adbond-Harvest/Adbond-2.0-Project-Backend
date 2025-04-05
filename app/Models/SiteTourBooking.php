<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteTourBooking extends Model
{
    use HasFactory;

    public function bookedSchedule()
    {
        return $this->belongsTo(SiteTourBookedSchedule::class, "booked_schedules_id", "id");
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
