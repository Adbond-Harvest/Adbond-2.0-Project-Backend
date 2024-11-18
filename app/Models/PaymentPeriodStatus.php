<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use app\Enums\PaymentPeriodStatus as Status;

class PaymentPeriodStatus extends Model
{
    use HasFactory;

    public static function grace()
    {
        return self::where("name", Status::GRACE->value)->first();
    }

    public static function normal()
    {
        return self::where("name", Status::NORMAL->value)->first();
    }

    public static function penalty()
    {
        return self::where("name", Status::PENALTY->value)->first();
    }
}
