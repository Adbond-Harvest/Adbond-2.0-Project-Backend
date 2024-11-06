<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\PaymentMode;

use app\Enums\PaymentMode as PaymentModeEnum;

class PaymentModes extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentModes = [
            PaymentModeEnum::BANK_TRANSFER->value,
            PaymentModeEnum::CARD_PAYMENT->value
        ];

        foreach($paymentModes as $mode) {
            PaymentMode::firstOrCreate([
                "name" => $mode
            ]);
        }
    }
}
