<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteTourBookedSchedule extends Model
{
    use HasFactory;

    public function schedule()
    {
        return $this->belongsTo(SiteTourSchedule::class, "site_tour_schedule_id", "id");
    }

    public function bookings()
    {
        return $this->hasMany(SiteTourBooking::class, "booked_schedules_id", "id");
    }
}
