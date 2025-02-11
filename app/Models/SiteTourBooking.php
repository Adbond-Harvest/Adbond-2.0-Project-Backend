<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteTourBooking extends Model
{
    use HasFactory;

    public function schedule()
    {
        return $this->belongsTo(SiteTourSchedule::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
