<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use app\Enums\ProjectType as ProjectTypeEnum;

class ProjectType extends Model
{
    use HasFactory;

    public static function land()
    {
        return self::where("name", ProjectTypeEnum::LAND->value);
    }

    public static function agro()
    {
        return self::where("name", ProjectTypeEnum::AGRO->value);
    }

    public static function homes()
    {
        return self::where("name", ProjectTypeEnum::HOMES->value);
    }

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
