<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\PostType;

class PostTypes extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            "news", "events", "offers", "blog", "promotions"
        ];

        foreach($types as $type) PostType::firstOrCreate([ "name" => $type]);
    }
}
