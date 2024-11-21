<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_type_id',
        'installment',
        'direct',
        'indirect'
    ];
}
