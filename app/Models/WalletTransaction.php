<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WalletTransaction extends Model
{
    use HasFactory;

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(WalletBankAccount::class, "wallet_bank_account_id", "id");
    }

    // public function package()
    // {
    //     return $this->belongsTo(Package::class);
    // }

    /**
     * Get the parent purchase model (Order or Offer).
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function WithdrawalRequest()
    {
        return $this->belongsTo(WalletWithdrawalRequest::class, "withdrawal_request_id", "id");
    }
}
