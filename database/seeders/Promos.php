<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\Promo;
use app\Models\User;

class Promos extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promos = [
            [
                "title" => "Welcome Promo",
                "discount" => 5,
                "start" => null,
                "end" => null,
                "description" => "Promo for new Clients",
                "userId" => 1
            ]
        ];

        foreach($promos as $promo) {
            Promo::FirstOrCreate([
                "title" => $promo['title'],
                "discount" => $promo['discount'],
                "start" => $promo['start'],
                "end" => $promo['end'],
                "description" => $promo['description'],
                "user_id" => User::first()->id
            ]);
        }
    }
}
