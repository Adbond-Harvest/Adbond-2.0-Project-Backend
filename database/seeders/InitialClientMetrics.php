<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\Client;
use app\Models\ClientPackage;
use app\Models\ClientMetric;

class InitialClientMetrics extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $activeCount = Client::whereHas("assets")->count();
        // $totalCount = Client::count();
        $clients = Client::all();
        $assets = ClientPackage::selectRaw('MIN(created_at) as created_at')->groupBy('client_id')->pluck('created_at')->toArray();
        // dd($assets[0]);
        if($clients->count() > 0) {
            $total = 0;
            $active = 0;
            foreach($clients as $client) {
                $activated = !empty($assets);
                while($activated){
                    if(!empty($assets)) {
                        $firstAssetDate = $assets[0]; // This is now a string
                        // dd($firstAssetDate); // Debugging output
                        // dd($client->created_at->isSameDay($firstAssetDate));
                        if($client->created_at->isSameDay($firstAssetDate) || $client->created_at->isAfter($firstAssetDate)) {
                            $metric = new ClientMetric;
                            $metric->previous_active_total = $active;
                            $metric->active_total = ++$active;
                            $metric->created_at = $firstAssetDate;
                            $metric->updated_at = $firstAssetDate;
                            $metric->save();
                            array_shift($assets);
                            $activated = !empty($assets);
                        }else{
                            $activated = false;
                        }
                    }
                }
                $metric = new ClientMetric;
                $metric->previous_total = $total;
                $metric->total = ++$total;

                $metric->created_at = $client->created_at;
                $metric->updated_at = $client->created_at;
                $metric->save();
            }

        }

        if(!empty($assets)) {
            foreach($assets as $assetDate) {
                $metric = new ClientMetric;
                $metric->previous_active_total = $active;
                $metric->active_total = ++$active;
                $metric->created_at = $assetDate;
                $metric->updated_at = $assetDate;
                $metric->save();
            } 
        }
    }
}
