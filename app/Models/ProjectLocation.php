<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectLocation extends Model
{
    use HasFactory;

    public function project()
    {
        return $this->belongsTo("app\Models\Project");
    }

    public function state()
    {
        return $this->belongsTo("app\Models\State");
    }
}
