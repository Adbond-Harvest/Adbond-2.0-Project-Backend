<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAssessmentAnswer extends Model
{
    use HasFactory;

    public function attempt()
    {
        return $this->belongsTo(StaffAssessmentAttempt::class, "staff_assessment_attempt_id", "id");
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption()
    {
        return $this->belongsTo(QuestionOption::class, "selected_option_id", "id");
    }
}
