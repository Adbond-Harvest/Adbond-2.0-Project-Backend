<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->morphTo();
    }

    public function entity()
    {
        return $this->morphTo();
    }
}
