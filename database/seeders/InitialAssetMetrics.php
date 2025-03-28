<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\ClientPackage;
use app\Models\AssetMetric;

class InitialAssetMetrics extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = ClientPackage::all();   
        
        if($assets->count() > 0) {
            $total = 0;
            $active = 0;
            $updatedDates = [];
            $purchasedDates = [];
            foreach($assets as $asset) {
                $updated = !empty($updatedDates);
                while($updated){
                    if(!empty($updatedDates)) {
                        $dateUpdated = $updatedDates[0];
                        if($asset->created_at->isSameDay($dateUpdated) || $asset->created_at->isAfter($dateUpdated)) {
                            $metric = new AssetMetric;
                            $metric->previous_active_total = $active;
                            $metric->active_total = ++$active;
                            $metric->created_at = $dateUpdated;
                            $metric->updated_at = $dateUpdated;
                            $metric->save();
                            array_shift($updatedDates);
                            $updated = !empty($updatedDates);
                        }else{
                            $updated = false;
                        }
                    }
                }

                $purchased = !empty($purchasedDates);
                while($purchased){
                    if(!empty($purchasedDates)) {
                        $datePurchased = $purchasedDates[0];
                        if($asset->created_at->isSameDay($datePurchased) || $asset->created_at->isAfter($datePurchased)) {
                            $metric = new AssetMetric;
                            $metric->previous_active_total = $active;
                            $metric->active_total = --$active;
                            $metric->created_at = $datePurchased;
                            $metric->updated_at = $datePurchased;
                            $metric->save();
                            array_shift($purchasedDates);
                            $purchased = !empty($purchasedDates);
                        }else{
                            $purchased = false;
                        }
                    }
                }

                $metric = new AssetMetric;
                $metric->previous_total = $total;
                $metric->total = ++$total;
                if($asset->purchase_complete == 0) {
                    $metric->previous_active_total = $active;
                    $metric->active_total = ++$active;
                }
                $metric->created_at = $asset->created_at;
                $metric->updated_at = $asset->created_at;
                
                if($asset->purchase_complete == 1  && !$asset->created_at->isSameDay($asset->updated_at)) {
                    $updatedDates[] = $asset->created_at;
                    $purchasedDates[] = ($asset->purchase_completed_at) ? $asset->purchase_completed_at : $asset->updated_at;
                }

                $metric->save();
            }
        }
    }
}
