<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\ProjectType;
use app\Services\ProjectService;

use app\Enums\ProjectType as ProjectTypeEnum;

class Projects extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                "name" => "Ibeju Country Home",
                "projectType" => ProjectTypeEnum::LAND->value,
                "description" => "This is the Projects for Ibeju Country homes"
            ],
            [
                "name" => "Legacy Green HomeStead",
                "projectType" => ProjectTypeEnum::LAND->value,
                "description" => "This is the Projects for Legacy Green Homestead"
            ],
            [
                "name" => "New Lagos City",
                "projectType" => ProjectTypeEnum::LAND->value,
                "description" => "This is the Projects for New Lagos City"
            ],
            [
                "name" => "Country Home Motherland",
                "projectType" => ProjectTypeEnum::LAND->value,
                "description" => "This is the Projects for Country Home Motherland"
            ],
            [
                "name" => "Ibeju Country Home 2",
                "projectType" => ProjectTypeEnum::LAND->value,
                "description" => "This is the Projects for Ibeju Country homes Phase 2"
            ],
            [
                "name" => "Heritage City Homestead",
                "projectType" => ProjectTypeEnum::LAND->value,
                "description" => "This is the Projects for Heritage City Homestead"
            ],
            [
                "name" => "Smart Homes Estate",
                "projectType" => ProjectTypeEnum::HOMES->value,
                "description" => "This is the Projects Under Smart Homes Estate"
            ],
            [
                "name" => "Green Project",
                "projectType" => ProjectTypeEnum::AGRO->value,
                "description" => "This is the Projects Under Green Project"
            ]
        ];

        $projectService = new ProjectService;
        foreach($projects as $project) {
            $projectType = ProjectType::whereName($project['projectType'])->first();
            if($projectType) {
                $projectObj = $projectService->getProjectByName($project['name'], $projectType->id);
                if(!$projectObj) {
                    $project['projectTypeId'] = $projectType->id;
                    $projectService->save($project);
                }
            }
        }
    }
}
