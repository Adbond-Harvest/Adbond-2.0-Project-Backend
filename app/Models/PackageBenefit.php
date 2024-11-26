<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageBenefit extends Model
{
    use HasFactory;

    public function benefit()
    {
        return $this->belongsTo(Benefit::class);
    }
}
