<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function attempts()
    {
        return $this->hasMany(AssessmentAttempt::class);
    }

    protected static function boot()
    {
        parent::boot();

        self::deleting(function (Assessment $assessment) {
            if($assessment->questions->count() > 0) {
                foreach($assessment->questions as $question) $question->delete();
            }
        });
    }
}
