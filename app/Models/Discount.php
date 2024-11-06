<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use app\Enums\DiscountType;

class Discount extends Model
{
    use HasFactory;

    public static function fullPayment()
    {
        return self::whereType(DiscountType::FULL_PAYMENT->value)->first();
    }

    public static function loyalty()
    {
        return self::whereType(DiscountType::LOYALTY->value)->first();
    }
}
