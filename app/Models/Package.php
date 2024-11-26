<?php

namespace app\Models;

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
        return $this->belongsTo("app\Models\State");
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

    public function benefits()
    {
        return $this->belongsToMany(Benefit::class, "package_benefits", "package_id", "benefit_id", "id");
    }

    public function packageBenefits()
    {
        return $this->hasMany(PackageBenefit::class);
    }

    public function media()
    {
        return $this->belongsToMany(File::class, "package_media", "package_id", "file_id", "id");
    }

    /**
     * Get all promos for this package
     */
    public function promos()
    {
        return $this->morphMany(PromoProduct::class, 'product');
    }
}
