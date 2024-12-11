<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Offer extends Model
{
    use HasFactory;

    /**
     * Get all client packages for this offer
     */
    public function clientPackages(): MorphMany
    {
        return $this->morphMany(ClientPackage::class, 'purchase');
    }

    /**
     * Get all payments for this order
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'purchase');
    }
}
