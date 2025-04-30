<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    /**
     * Get all packages associated with this promo
     */
    public function packages()
    {
        return $this->morphedByMany(Package::class, 'product', 'promo_products');
    }

    /**
     * Get all projects associated with this promo
     */
    public function projects()
    {
        return $this->morphedByMany(Project::class, 'product', 'promo_products');
    }

    /**
     * Get all promo products (both packages and projects)
     */
    public function promoProducts()
    {
        return $this->hasMany(PromoProduct::class);
    }

    public function promoCodes()
    {
        return $this->hasMany(PromoCode::class);
    }

    protected static function boot()
    {
        parent::boot();

        self::deleting(function (Promo $promo) {
            if($promo->promoCodes->count() > 0) {
                foreach($promo->promoCodes as $promoCode) {
                    $promoCode->delete();
                }
            }

            if($promo->promoProducts->count() > 0) {
                foreach($promo->promoProducts as $promoProduct) {
                    $promoProduct->delete();
                }
            }
        });
    }
}
