<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\PaymentPeriodStatus;

use app\Enums\PaymentPeriodStatus as PaymentPeriodStatusEnum;

class PaymentPeriodStatuses extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentPeriods = [
            PaymentPeriodStatusEnum::GRACE->value, 
            PaymentPeriodStatusEnum::NORMAL->value,
            PaymentPeriodStatusEnum::PENALTY->value
        ];

        foreach($paymentPeriods as $paymentPeriod) {
            PaymentPeriodStatus::firstOrCreate([
                "name" => $paymentPeriod
            ]);
        }
    }
}
