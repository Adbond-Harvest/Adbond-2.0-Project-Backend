<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Offer extends Model
{
    use HasFactory;

    public static $type = "app\Models\Offer";

    public function asset()
    {
        return $this->belongsTo(ClientPackage::class, 'client_package_id');
    }

    public function paymentStatus()
    {
        return $this->belongsTo(PaymentStatus::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resellOrder()
    {
        return $this->belongsTo(ResellOrder::class);
    }

    /**
     * Get all client packages for this offer
     */
    public function clientPackage()
    {
        return $this->hasOne(ClientPackage::class, 'purchase_id');
    }

    /**
     * Get all payments for this order
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'purchase');
    }

    public function bids()
    {
        return $this->hasMany(OfferBid::class);
    }

    public function acceptedBid()
    {
        return $this->bids()->where('cancelled', 0)->where("accepted", 1)->first();
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($offer) {
            if($offer->resell_order_id) {
                $offer->approved = 1;
            }
        });
    }

}
