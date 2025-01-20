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
        return $this->hasMany(WalletTransaction::class);
    }

    public function withdrawalRequests()
    {
        return $this->hasMany(WalletWithdrawalRequest::class);
    }

    protected static function boot()
    {
        parent::boot();

        // static::creating(function ($order) {
        //     $order->payment_period_status_id = PaymentPeriodStatus::normal()->id;
        //     // if(!$order->balance) $order->bala
        //     if($order->balance < 0) $order->balance = 0;
        // });
        self::updating(function (Wallet $wallet) {
            if($wallet->locked_amount < 0) $wallet->locked_amount = 0;
        });
    }
}
