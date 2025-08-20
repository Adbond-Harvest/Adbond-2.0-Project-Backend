<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferBid extends Model
{
    use HasFactory;

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function paymentStatus()
    {
        return $this->belongsTo(PaymentStatus::class);
    }
}
