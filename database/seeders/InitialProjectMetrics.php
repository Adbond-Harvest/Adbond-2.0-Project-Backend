<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\Project;
use app\Models\ProjectMetric;

class InitialProjectMetrics extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::all();
        if($projects->count() > 0) {
            $total = 0;
            $active = 0;
            $updatedDates = [];
            foreach($projects as $project) {
                // $latestTotalMetric = ProjectMetric::whereNotNUll("total")->orderBy("created_at", "desc");
                // $latestActiveMetric = ProjectMetric::whereNotNUll("active_total")->orderBy("created_at", "desc");
                $updated = !empty($updatedDates);
                while($updated){
                    if(!empty($updatedDates)) {
                        $dateUpdated = $updatedDates[0];
                        if($project->created_at->isSameDay($dateUpdated) || $project->created_at->isAfter($dateUpdated)) {
                            $metric = new ProjectMetric;
                            $metric->previous_active_total = $active;
                            $metric->active_total = --$active;
                            $metric->created_at = $dateUpdated;
                            $metric->updated_at = $dateUpdated;
                            $metric->save();
                            array_shift($updatedDates);
                            $updated = !empty($updatedDates);
                        }else{
                            $updated = false;
                        }
                    }
                }
                $metric = new ProjectMetric;
                $metric->previous_total = $total;
                $metric->total = ++$total;
                $metric->previous_active_total = $active;
                $metric->active_total = ++$active;

                if($project->active == 0) {
                    if($project->created_at != $project->updated_at) {
                        $updatedDates[] = ($project->deactivated_at) ? $project->deactivated_at : $project->updated_at;
                    }else{
                        $metric->previous_active_total = $active;
                        $metric->active_total = --$active;
                    }
                }
                $metric->created_at = $project->created_at;
                $metric->updated_at = $project->created_at;
                $metric->save();
            }
        }
        // $activeCount = Project::where("active", 1)->count();
        // $totalCount = Project::count();

        // $projectMetric = new ProjectMetric;
        // $projectMetric->total = $totalCount;
        // $projectMetric->previous_total = 0;
        // $projectMetric->active_total = $activeCount;
        // $projectMetric->previous_active_total = 0;
        // $projectMetric->save();
    }
}
