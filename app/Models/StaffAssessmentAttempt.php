<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAssessmentAttempt extends Model
{
    use HasFactory;

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}
