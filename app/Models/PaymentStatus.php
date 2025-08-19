<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use app\Enums\PaymentStatus as Status;

class PaymentStatus extends Model
{
    use HasFactory;

    public static function deposit()
    {
        return self::where("name", Status::DEPOSIT->value)->first();
    }

    public static function pending()
    {
        return self::where("name", Status::PENDING->value)->first();
    }

    public static function awaiting_payment()
    {
        return self::where("name", Status::AWAITING_PAYMENT->value)->toSql();
    }

    public static function complete()
    {
        return self::where("name", Status::COMPLETE->value)->first();
    }
}
