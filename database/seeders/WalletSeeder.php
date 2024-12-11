<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\Wallet;
use app\Models\Client;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();

        if($clients->count() > 0) {
            foreach($clients as $client) {
                Wallet::firstOrCreate([
                    "client_id" => $client->id
                ]);
            }
        }
    }
}
