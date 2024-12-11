<?php

namespace app\Http\Controllers\User\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use app\Http\Controllers\Controller;

use app\Http\Requests\User\AddWalletBankAccount;

use app\Http\Resources\WalletResource;

use app\Services\WalletService;


use app\Utilities;

class WalletController extends Controller
{
    private $walletService;

    public function __construct()
    {
        $this->walletService = new WalletService;
    }

    public function LinkBankAccount(AddWalletBankAccount $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->validated();
            $wallet = $this->walletService->wallet($data['walletId']);
            if(!$wallet) return Utilities::error402("Wallet not found");

            $this->walletService->addBankAccount($wallet, $data);
            $wallet = $this->walletService->wallet($wallet->id, ['bankAccounts']);

            DB::commit();
            return Utilities::ok(new WalletResource($wallet));
        }catch(\Exception $e){
            DB::rollBack();
            return Utilities::error($e, 'An error occurred while trying to send verification mail, Please try again later or contact support');
        }
    }
}
