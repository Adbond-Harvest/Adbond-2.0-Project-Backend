<?php

namespace app\Http\Controllers\Client;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use app\Http\Controllers\Controller;

use app\Http\Requests\Client\WalletWithdrawal;

use app\Http\Resources\WalletWithdrawalRequestResource;

use app\Services\WalletService;

use app\Utilities;

class WalletController extends Controller
{
    private $walletService;

    public function __construct()
    {
        $this->walletService = new WalletService;
    }

    public function withdraw(WalletWithdrawal $request)
    {
        try{
            $data = $request->validated();
            $wallet = $this->walletService->wallet($data['walletId']);

            if(!$wallet) return Utilities::error402("Wallet not found");

            if($wallet->client_id != Auth::guard("client")->user()->id) return Utilities::error402("You cannot withdraw from a wallet you dont own");

            $availableAmount = $wallet->amount - $wallet->locked_amount;
            if($data['amount'] > $availableAmount) return Utilities::error402("The amount you want to withdraw is greater than available amount");

            $withdrawalRequest = $this->walletService->generateWithdrawalRequest($wallet, $data['amount']);

            return Utilities::ok(new WalletWithdrawalRequestResource($withdrawalRequest));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to perform this operation, Please try again later or contact support');
        }
    }
}
