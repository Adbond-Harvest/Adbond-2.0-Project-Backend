<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use app\Models\PaymentPeriodStatus;

class Order extends Model
{
    use HasFactory;

    public static $type = "app\Models\Order";

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function paymentStatus()
    {
        return $this->belongsTo(PaymentStatus::class);
    }

    /**
     * Get all payments for this order
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'purchase');
    }

    public function discounts()
    {
        return $this->hasMany(OrderDiscount::class);
    }

    /**
     * Get all client packages for this order
     */
    public function clientPackages(): MorphMany
    {
        return $this->morphMany(ClientPackage::class, 'purchase');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->payment_period_status_id = PaymentPeriodStatus::normal()->id;
            // if(!$order->balance) $order->bala
            if($order->balance < 0) $order->balance = 0;
        });
        self::updating(function (Order $order) {
            if($order->balance < 0) $order->balance = 0;
        });
    }
}
