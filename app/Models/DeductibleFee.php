<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use app\Enums\DeductibleFee as Enum;

class DeductibleFee extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "percentage"
    ];

    public static function commissionTax()
    {
        return self::where("name", Enum::COMMISSION_TAX->value)->first();
    }
}
