<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benefit extends Model
{
    protected $table = "benefits";
    
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
    ];
}
