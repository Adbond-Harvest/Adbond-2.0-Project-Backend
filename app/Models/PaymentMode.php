<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use app\Enums\PaymentMode as PaymentModeEnum;

class PaymentMode extends Model
{
    use HasFactory;

    public static function cash()
    {
        return self::whereName(PaymentModeEnum::CASH->value)->first();
    }

    public static function bankTransfer()
    {
        return self::whereName(PaymentModeEnum::BANK_TRANSFER->value)->first();
    }

    public static function cardPayment()
    {
        return self::whereName(PaymentModeEnum::CARD_PAYMENT->value)->first();
    }
}
