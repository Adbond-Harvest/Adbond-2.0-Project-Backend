<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function correctOption()
    {
        return $this->options->where('answer', 1)->first();
    }

    protected static function boot()
    {
        parent::boot();

        self::deleting(function (Question $question) {
            if($question->options->count() > 0) {
                foreach($question->options as $option) $option->delete();
            }
        });
    }
}
