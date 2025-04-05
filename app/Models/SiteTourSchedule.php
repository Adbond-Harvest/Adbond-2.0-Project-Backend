<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteTourSchedule extends Model
{
    use HasFactory;

    public function projectType()
    {
        return $this->belongsTo("app\Models\ProjectType");
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function booked()
    {
        return $this->hasMany(SiteTourBookedSchedule::class, "site_tour_schedule_id", "id");
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'site_tour_bookings', 'site_tour_schedule_id', 'client_id', 'id', 'id');
    }
}
