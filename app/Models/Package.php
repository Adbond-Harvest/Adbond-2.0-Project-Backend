<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'benefits' => 'array',
        'active' => 'boolean',
        'installment_option' => 'boolean',
        'size' => 'double',
        'amount' => 'double',
        'discount' => 'double',
        'min_price' => 'double',
        'infrastructure_fee' => 'double'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function state()
    {
        return $this->belongsTo("App\Models\State");
    }

    public function brochure()
    {
        return $this->belongsTo(File::class);
    }

    // public function sizes()
    // {
    //     return $this->hasMany(PackageSize::class);
    // }

    public function packagePhotos()
    {
        return $this->hasMany(PackagePhoto::class);
    }

    public function photos()
    {
        return $this->belongsToMany(File::class, "package_photos", "package_id", "photo_id", "id");
    }
}
