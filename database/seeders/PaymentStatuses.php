<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\PaymentStatus;

use app\Enums\PaymentStatus as PaymentStatusEnum;

class PaymentStatuses extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentStatuses = [
            PaymentStatusEnum::COMPLETE->value,
            PaymentStatusEnum::DEPOSIT->value,
            PaymentStatusEnum::PENDING->value
        ];

        foreach($paymentStatuses as $status) {
            PaymentStatus::firstOrCreate([
                "name" => $status
            ]);
        }
    }
}
