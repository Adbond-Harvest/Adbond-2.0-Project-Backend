<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffTotalEarningView extends Model
{
    protected $table = 'staff_total_earnings';

    public $timestamps = false;

    protected $guarded = [];

    public $incrementing = false;
}
