<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Services\ClientInvestmentService;
use app\Services\WalletService;

class InvestmentRedemptions extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $walletService = new WalletService;
        $clientInvestmentService = new ClientInvestmentService;
        $runningInvestments = $clientInvestmentService->runningInvestments();

        if($runningInvestments->count() > 0) {
            foreach($runningInvestments as $investment) {
                $wallet = $walletService->clientWallet($investment->client_id);
                $profit = $clientInvestmentService->getProfit($investment);
                $walletService->creditInvestmentProfit($wallet, $investment, $profit);
            }
        }
    }
}
