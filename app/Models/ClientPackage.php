<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ClientPackage extends Model
{
    use HasFactory;

    /**
     * Get the parent purchase model (Order or Offer).
     */
    public function purchase(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relationship to Client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relationship to Package
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    
}
