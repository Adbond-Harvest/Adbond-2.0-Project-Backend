<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\ProjectType;

use app\Enums\ProjectType as ProjectTypeEnum;

class ProjectTypes extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projectTypes = [
            [
                'name' => ProjectTypeEnum::LAND->value, 
                'description' => 'Adbond Harvest and Homes provides land in bond, retail and whole in plots, acres, and hectares to individuals, groups, societies, and large corporations to own a space. You can secure lands at cost-effective prices with available flexible payment plans.', 
                'order' => 1
            ], 
            [
                'name' => ProjectTypeEnum::AGRO->value, 
                'description' => 'We offer agro-engagement options  through annual rental income on the engagement at affordable prices with flexible payment plans.', 
                'order' => 2
            ],
            [
                'name' => ProjectTypeEnum::HOMES->value, 
                'description' => 'At Adbond, we provide home hospitality development for clients to conveniently live in, and enjoy serene environments at affordable prices, with flexible payment plans.', 
                'order' => 3
            ]
        ];

        foreach($projectTypes as $type) {
            $projectType = ProjectType::where("name", $type['name'])->first();
            if(!$projectType) {
                $projectType = new ProjectType;
                $projectType->name = $type['name'];
                $projectType->description = $type['description'];
                $projectType->order = $type['order'];
                $projectType->save();
            }
        }
    }
}
