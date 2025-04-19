<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentAttempt extends Model
{
    use HasFactory;

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function answers()
    {
        return $this->hasMany(AssessmentAttemptAnswer::class, "attempt_id", "id");
    }

    protected static function boot()
    {
        parent::boot();

        self::deleting(function (AssessmentAttempt $attempt) {
            if($attempt->answers->count() > 0) {
                foreach($attempt->answers as $answer) $answer->delete();
            }
        });
    }
}
