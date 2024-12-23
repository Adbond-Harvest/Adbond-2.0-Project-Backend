<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageSize extends Model
{
    use HasFactory;

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
