<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectLocation extends Model
{
    use HasFactory;

    public function project()
    {
        return $this->belongsTo("App\Models\Project");
    }

    public function state()
    {
        return $this->belongsTo("App\Models\State");
    }
}
