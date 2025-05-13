<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffTotalRedemptionView extends Model
{
    protected $table = 'staff_total_redemptions';

    public $timestamps = false;

    protected $guarded = [];

    public $incrementing = false;
}
