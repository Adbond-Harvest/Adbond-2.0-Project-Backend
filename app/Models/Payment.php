<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use app\Models\PaymentMode;

class Payment extends Model
{
    use HasFactory;


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if(!$payment->confirmed && $payment->payment_mode_id) {
                $payment->confirmed = ($payment->payment_mode_id == PaymentMode::cardPayment()->id);
            }else{
                $payment->confirmed = false;
            }
        });
    }
}
