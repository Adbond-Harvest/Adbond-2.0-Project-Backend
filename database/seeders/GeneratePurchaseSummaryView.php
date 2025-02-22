<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\ClientPackage;

class GeneratePurchaseSummaryView extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientPackages = ClientPackage::all();

        if($clientPackages->count() > 0) {
            foreach($clientPackages as $clientPackage) {
                $clientPackage->purchase_completed_at = explode(" ", $clientPackage->updated_at)[0];
                $clientPackage->update();
            }
        }
    }
}
