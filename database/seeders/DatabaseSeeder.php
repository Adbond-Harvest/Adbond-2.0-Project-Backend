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
use Database\Seeders\Projects;
use Database\Seeders\Packages;
use Database\Seeders\Promos;
use Database\Seeders\PromoCodes;
use Database\Seeders\Discounts;
use Database\Seeders\PaymentModes;
use Database\Seeders\PaymentPeriodStatuses;
use Database\Seeders\PaymentStatuses;
use Database\Seeders\PaymentGateways;

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
            new Users,
            new Projects,
            new Packages,
            new Promos,
            new PromoCodes,
            new Discounts,
            new PaymentModes,
            new PaymentPeriodStatuses,
            new PaymentStatuses,
            new PaymentGateways
        ];

        foreach($seeders as $seeder) $seeder->run();

    }
}
