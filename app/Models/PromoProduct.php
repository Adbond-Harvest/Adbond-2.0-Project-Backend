<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoProduct extends Model
{
    use HasFactory;

    /**
     * Get the parent product model (Project or Package)
     */
    public function product()
    {
        return $this->morphTo();
    }

    /**
     * Get the promo that owns this product
     */
    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }
}
