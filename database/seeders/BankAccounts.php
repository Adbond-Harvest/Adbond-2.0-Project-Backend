<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\Bank;
use app\Models\BankAccount;

class BankAccounts extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bankAccounts = BankAccount::all();
        $total = $bankAccounts->count();
        $count = 2 - $total;
        if($count > 0) {
            $bankIds = Bank::pluck("id")->toArray();
            for($i=$total; $i<$count; $i++) {
                $bankId = $bankIds[array_rand($bankIds)];
                $accountNumber = rand(10000000000, 99999999999); 
                $bankAccount = new BankAccount;
                $bankAccount->bank_id = $bankId;
                $bankAccount->number = $accountNumber;
                $bankAccount->name = "Adbond Harvest and Homes";
                $bankAccount->save();
            }
        }
    }
}
