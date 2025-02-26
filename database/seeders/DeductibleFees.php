<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\Deduct1bleFee;

use app\Enums\DeductibleFee as Enum;
use app\Models\DeductibleFee;

class DeductibleFees extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fees = [
            ["name" => Enum::COMMISSION_TAX->value, "percentage" => 7.5], 
            ["name" => Enum::DOWNGRADE_PENALTY->value, "percentage" => "7"]
        ];

        foreach($fees as $fee) {
            $deductible = DeductibleFee::where("name", $fee['name'])->first();
            if(!$deductible) $deductible = new DeductibleFee;

            $deductible->name = $fee['name'];
            $deductible->percentage = $fee['percentage'];
            $deductible->save();
        }
    }
}
