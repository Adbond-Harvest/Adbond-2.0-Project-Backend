<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

use app\Models\Benefit;

class Benefits extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $benefits = [
            ["name" => "Survey Plan", "icon" => env('APP_URL') . Storage::url('public/benefits/Survey Plan.svg')],
            ["name" => "Communal Living", "icon" => env('APP_URL') . Storage::url('public/benefits/Communal Living.svg')],
            ["name" => "Good Drainage System", "icon" => env('APP_URL') . Storage::url('public/benefits/Good Drainage System.svg')],
            ["name" => "Good Road Network", "icon" => env('APP_URL') . Storage::url('public/benefits/Good Road Network.svg')],
            ["name" => "Perimeter Fencing", "icon" => env('APP_URL') . Storage::url('public/benefits/Perimeter Fencing.svg')],
            ["name" => "Power Supply", "icon" => env('APP_URL') . Storage::url('public/benefits/Power Supply.svg')],
        ];

        foreach($benefits as $benefit) {
            $benefitObj = Benefit::where("name", $benefit['name']);
            if(!$benefitObj) {
                $benefitObj->name = $benefit['name'];
                $benefitObj->icon = $benefit['icon'];
                $benefitObj->save();
            }
            // Benefit::firstOrCreate([
            //     "name" => $benefit['name'],
            //     "icon" => $benefit['icon']
            // ]);
        }
    }
}
