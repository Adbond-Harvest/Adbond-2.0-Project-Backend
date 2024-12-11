<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public static $type = "app\Models\Post";

    public function file()
    {
        return $this->belongsTo(File::class, "file_id", "id");
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
