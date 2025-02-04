<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\ResellOrder;

class ResellOrders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resellOrders = [
            ["percentage" => 50, "duration" => 90, "duration_type" => "days", "duration_text" => "90 days"],
            ["percentage" => 60, "duration" => 180, "duration_type" => "days", "duration_text" => "180 days"],
            ["percentage" => 100, "duration" => 18, "duration_type" => "months", "duration_text" => "12 - 18 Months"]
        ];
        foreach($resellOrders as $resellOrder) {
            $resellOrderObj = new ResellOrder;
            $resellOrderObj->percentage = $resellOrder['percentage'];
            $resellOrderObj->duration = $resellOrder['duration'];
            $resellOrderObj->duration_type = $resellOrder['duration_type'];
            $resellOrderObj->duration_text = $resellOrder['duration_text'];
            $resellOrderObj->save();
        }
    }
}
