<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\CommissionRate;
use app\Models\ClientCommissionRate;
use app\Models\StaffType;

use app\Enums\StaffTypes;

class CommissionRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffTypes = StaffType::all();
        // for full payment
        foreach($staffTypes as $staffType) {
            CommissionRate::firstOrCreate([
                "staff_type_id" => $staffType->id,
                "installment" => 0,
                "direct" => ($staffType->name == StaffTypes::VIRTUAL_STAFF->value) ? 10 : 12,
                "indirect" => ($staffType->name == StaffTypes::VIRTUAL_STAFF->value) ? null : 2
            ]);
        }
        // For installment
        CommissionRate::firstOrCreate([
            "staff_type_id" => $staffType->id,
            "installment" => 1,
            "direct" => ($staffType->name == StaffTypes::VIRTUAL_STAFF->value) ? 5 : 7,
            "indirect" => ($staffType->name == StaffTypes::VIRTUAL_STAFF->value) ? null : 2
        ]);

        $rate = ClientCommissionRate::first();
        if(!$rate) {
            $rate = new ClientCommissionRate;
            $rate->rate = 10;
            $rate->save();
        }
    }
}
