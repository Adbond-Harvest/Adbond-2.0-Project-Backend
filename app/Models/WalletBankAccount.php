<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletBankAccount extends Model
{
    use HasFactory;

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
