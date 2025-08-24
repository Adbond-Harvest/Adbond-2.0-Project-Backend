<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use app\Services\WalletService;

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

    protected static function boot()
    {
        parent::boot();

        static::created(function ($earning) {
            $client = $earning->client;
            $wallet = $client->wallet;
            $walletService = new WalletService;
            $walletService->creditCommissionEarning($wallet, $earning);
        });
    }
}
