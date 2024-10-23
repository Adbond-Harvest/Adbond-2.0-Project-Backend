<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

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
