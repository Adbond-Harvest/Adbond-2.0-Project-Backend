<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    // public function getCreatedAtAttribute($value)
    // {
    //     return \Carbon\Carbon::parse($value)->diffForHumans();
    // }
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function canDelete()
    {
        return ($this->packages->count() == 0);
    }

    public function projectType()
    {
        return $this->belongsTo("app\Models\ProjectType");
    }

    // public function locations()
    // {
    //     return $this->hasMany("app\Models\ProjectLocation");
    // }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    /**
     * Get all promos for this project
     */
    public function promos()
    {
        return $this->morphMany(PromoProduct::class, 'product');
    }


}
