<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Faker\Factory as Faker;

use app\Models\Project;
use app\Models\State;
use app\Models\User;
use app\Services\PackageService;

class Packages extends Seeder
{
    private $packageTypes = [
        'Premium', 'Standard', 'Deluxe', 'Executive', 'Classic',
        'Elite', 'Comfort', 'Luxury', 'Basic', 'Superior'
    ];

    private $benefitsList = [
        'Fertile Soil',
        'Developing Area',
        'Close to Highway',
        'Gated Community',
        'Good Drainage',
        'Proximity to Market',
        'Easy Access Road',
        'Green Area',
        'Recreational Facilities',
        'Security Post',
        'Street Lights',
        'Water Supply',
        'Electricity',
        'School Nearby',
        'Hospital Nearby'
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $packageService = new PackageService;
        $user = User::first();

        if (!$user) {
            Log::error('No user found for package seeding');
            return;
        }

        // Get all projects and states
        $projects = Project::all();
        $states = State::all();

        // Generate 50 packages
        for ($i = 0; $i < 50; $i++) {
            try {
                $project = $projects->random();
                $state = $states->random();
                $packageType = $faker->randomElement($this->packageTypes);
                $purpose = $faker->randomElement(['Residential', 'Commercial', 'Mixed Use']);

                // Generate 2-4 random benefits
                $benefits = collect($this->benefitsList)
                    ->random(rand(2, 4))
                    ->values()
                    ->all();

                $size = $faker->randomElement([300, 450, 500, 600, 648, 700, 800, 1000]);
                $baseAmount = $faker->randomElement([500000, 750000, 1000000, 1500000, 2000000, 2500000]);
                
                $packageData = [
                    "name" => "{$packageType} {$purpose} Package " . ($i + 1),
                    "userId" => $user->id,
                    "project" => $project->name,
                    "state" => $state->name,
                    "address" => $faker->streetAddress . ", " . $state->name . " State",
                    "size" => $size,
                    "amount" => $baseAmount,
                    "units" => $faker->numberBetween(50, 200),
                    "discount" => $faker->randomElement([0, 5, 10, 15]),
                    "minPrice" => $baseAmount * 0.2, // 20% of amount as minimum price
                    "installmentDuration" => $faker->randomElement([6, 12, 18, 24]),
                    "infrastructureFee" => $faker->randomElement([100000, 150000, 200000, 250000]),
                    "description" => $faker->paragraph(3),
                    "benefits" => $benefits,
                    "installmentOption" => $faker->boolean(80), // 80% chance of having installment option
                    "projectId" => $project->id,
                    "stateId" => $state->id
                ];
                $packageService->projectId = $project->id;
                $existingPackage = $packageService->getByName($packageData['name']);
                
                if (!$existingPackage) {
                    $packageService->save($packageData);
                    // $this->command->info("Created package: {$packageData['name']}");
                } else {
                    // $this->command->info("Package already exists: {$packageData['name']}");
                }
            } catch (\Exception $e) {
                Log::error("Error seeding package " . ($packageData['name'] ?? 'Unknown') . ": " . $e->getMessage());
                // $this->command->error("Failed to create package: " . ($packageData['name'] ?? 'Unknown'));
            }
        }

        // Add your original specific packages
        $specificPackages = [
            [
                "name" => "Coper 1 & Undergraduate Package",
                "userId" => $user->id,
                "project" => "Ibeju Country Home 2",
                "state" => "Lagos",
                // ... rest of your original package data
            ],
            [
                "name" => "Pension and Children Package",
                "userId" => $user->id,
                "project" => "Ibeju Country Home 2",
                "state" => "Lagos",
                // ... rest of your original package data
            ],
        ];

        // Seed specific packages
        foreach($specificPackages as $packageData) {
            try {
                $project = Project::whereName($packageData['project'])->first();
                $state = State::whereName($packageData['state'])->first();

                if (!$project || !$state) {
                    continue;
                }

                $packageData['projectId'] = $project->id;
                $packageData['stateId'] = $state->id;

                $packageService->projectId = $project->id;
                $existingPackage = $packageService->getByName($packageData['name']);
                
                if (!$existingPackage) {
                    $packageService->save($packageData);
                    // $this->command->info("Created specific package: {$packageData['name']}");
                }
            } catch (\Exception $e) {
                Log::error("Error seeding specific package {$packageData['name']}: " . $e->getMessage());
            }
        }
    
    }
}
