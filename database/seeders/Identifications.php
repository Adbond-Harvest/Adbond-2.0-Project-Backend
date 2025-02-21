<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\Identification;

class Identifications extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $identifications = ["BVN", "NIN", "International Passport", "Drivers License"];

        foreach($identifications as $idName) {
            Identification::firstOrCreate(["name" => $idName]);
        } 
    }
}
