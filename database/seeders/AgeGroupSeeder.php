<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\AgeGroup;

class AgeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ageGroups = [
            ['start'=>18, 'end'=>24],
            ['start'=>25, 'end'=>34],
            ['start'=>35, 'end'=>44],
            ['start'=>45, 'end'=>54],
            ['start'=>55, 'end'=>64],
            ['start'=>65, 'end'=>100]
        ];

        foreach($ageGroups as $group) {
            $ageGroup = AgeGroup::where("start", $group['start'])->where("end", $group['end'])->first();
            if(!$ageGroup) {
                $ageGroup = new AgeGroup;
                $ageGroup->start = $group['start'];
                $ageGroup->end = $group['end'];
                $ageGroup->save();
            }
        }
    }
}
