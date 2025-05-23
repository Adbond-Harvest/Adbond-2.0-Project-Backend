<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
    use HasFactory;

    public static $type = "app\Models\Comment";

    /**
     * Get the parent comment model (User or Client).
     */
    public function commenter(): MorphTo
    {
        return $this->morphTo();
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'entity');
    }

    public function likes()
    {
        return $this->morphMany(Reaction::class, 'entity')->where('reaction', 1);
    }

    public function dislikes()
    {
        return $this->morphMany(Reaction::class, 'entity')->where('reaction', 0);
    }
}
