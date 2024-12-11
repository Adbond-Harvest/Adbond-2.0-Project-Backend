<?php

namespace app\Services;

// use app\Services\StaffTypeService;

use app\Models\Wallet;
use app\Models\WalletBankAccount;

use app\Helpers;
use app\Utilities;

/**
 * user service class
 */
class WalletService
{
    public function wallets($with=[])
    {
        return Wallet::with($with)->get();
    }

    public function wallet($id, $with=[])
    {
        return Wallet::with($with)->where("id", $id)->first();
    }

    public function clientWallet($clientId, $with=[])
    {
        return Wallet::with($with)->where("client_id", $clientId)->first();
    }

    public function addBankAccount($wallet, $bankData)
    {
        $bankAccount = new WalletBankAccount;
        $bankAccount->bank_id = $bankData['bankId'];
        $bankAccount->account_number = $bankData['accountNumber'];
        $bankAccount->account_name = $bankData['accountName'];
        $bankAccount->wallet_id = $wallet->id;
        $bankAccount->save();
    }

}
