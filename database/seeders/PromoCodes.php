<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\Promo;
use app\Models\PromoCode;

class PromoCodes extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $codes = [
            [
                "promo" => "Welcome Promo",
                "code" => "AB426",
                "expiry" => "2024-12-31",
                "maxUsage" => 1
            ]
        ];

        foreach($codes as $code) {
            $promo = Promo::whereTitle($code['promo'])->first();
            if($promo) {
                PromoCode::firstOrCreate([
                    "promo_id" => $promo->id,
                    "code" => $code['code'],
                    "expiry" => $code['expiry'],
                    "max_usage" => $code['maxUsage']
                ]);
            }
        }
    }
}
