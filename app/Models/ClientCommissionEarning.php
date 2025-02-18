<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCommissionEarning extends Model
{
    use HasFactory;

    public function client()
    {
        return $this->belongsTo(CLient::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
