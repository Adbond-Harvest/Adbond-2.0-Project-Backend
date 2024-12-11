<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ["client_id", "amount", "total"];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(WalletBankAccount::class);
    }

    public function transactions()
    {
        $this->hasMany(WalletTransaction::class);
    }

    public function withdrawalRequests()
    {
        $this->hasMany(WalletWithdrawalRequest::class);
    }
}
