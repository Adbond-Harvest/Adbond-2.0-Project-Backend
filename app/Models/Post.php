<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    use HasFactory;

    public static $type = "app\Models\Post";

    public function coverPhoto()
    {
        return $this->belongsTo(File::class, "file_id", "id");
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy("created_at", "DESC");
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

    public static function boot ()
    {
        parent::boot();

        self::deleting(function (Post $post) {
            //Storage::disk('s3')->delete($file->url);
            if($post->coverPhoto) $post->coverPhoto->delete();

            if($post->comments->count() > 0) {
                foreach($post->comments as $comment) {
                    $comment->delete();
                }
            }

            if($post->likes->count() > 0) {
                foreach($post->likes as $like) {
                    $like->delete();
                }
            }

            if($post->dislikes->count() > 0) {
                foreach($post->dislikes as $dislike) {
                    $dislike->delete();
                }
            }

            if($post->postTags->count() > 0) {
                foreach($post->postTags as $postTag) {
                    $postTag->delete();
                }
            }
        });
    }
}
