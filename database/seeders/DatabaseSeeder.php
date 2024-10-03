<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Database\Seeders\AgeGroupSeeder;
use Database\Seeders\Banks;
use Database\Seeders\ProjectTypes;
use Database\Seeders\Roles;
use Database\Seeders\StaffTypes;
use Database\Seeders\States;
use Database\Seeders\Users;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $seeders = [
            new AgeGroupSeeder,
            new Banks,
            new ProjectTypes,
            new Roles,
            new StaffTypes,
            new States,
            new Users
        ];

        foreach($seeders as $seeder) $seeder->run();

    }
}
