<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    use HasFactory;

    public function photo()
    {
        return $this->belongsTo(File::class, "file_id", "id");
    }

    public function projects()
    {
        return $this->hasMany("app\Models\Project");
    }

    public function activeProjects()
    {
        return $this->projects()->where("active", true);
    }
    
    public function inactiveProjects()
    {
        return $this->projects()->where("active", false);
    }

    public function packages()
    {
        return $this->hasManyThrough(Package::class, Project::class, 'project_type_id', 'project_id', 'id', 'id');
    }

}
