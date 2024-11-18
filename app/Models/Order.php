<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use app\Models\PaymentPeriodStatus;

class Order extends Model
{
    use HasFactory;

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function paymentStatus()
    {
        return $this->belongsTo(PaymentStatus::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function discounts()
    {
        return $this->hasMany(OrderDiscount::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->payment_period_status_id = PaymentPeriodStatus::normal()->id;
        });
    }
}
