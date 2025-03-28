<?php

namespace app\Services;

// use app\Services\StaffTypeService;

use Illuminate\Support\Facades\DB;

use app\Models\ProjectMetric;
use app\Models\AssetMetric;
use app\Models\ClientMetric;

use app\Helpers;
use app\Utilities;

use app\Enums\MetricType;
use app\Enums\MetricDuration;

/**
 * user service class
 */
class MetricService
{

    public function __construct()
    {
        // $this->staffTypeService = new StaffTypeService;
    }

    public function addProjectMetric($type, $increaseTotal=true, $increaseActive=true)
    {
        $latestTotal = ProjectMetric::orderBy("created_at", "desc")->whereNotNull("total")->first();
        $latestActive = ProjectMetric::orderBy("created_at", "desc")->whereNotNull("active_total")->first();
        $metric = new ProjectMetric;

        $this->addMetric($type, $metric, $latestTotal, $latestActive, $increaseTotal, $increaseActive);
    }

    public function addAssetMetric($type, $increaseTotal=true, $increaseActive=true)
    {
        $latestTotal = AssetMetric::orderBy("created_at", "desc")->whereNotNull("total")->first();
        $latestActive = AssetMetric::orderBy("created_at", "desc")->whereNotNull("active_total")->first();
        $metric = new AssetMetric;

        $this->addMetric($type, $metric, $latestTotal, $latestActive, $increaseTotal, $increaseActive);
    }

    public function addClientMetric($type, $increaseTotal=true, $increaseActive=true)
    {
        $latestTotal = ClientMetric::orderBy("created_at", "desc")->whereNotNull("total")->first();
        $latestActive = ClientMetric::orderBy("created_at", "desc")->whereNotNull("active_total")->first();
        $metric = new ClientMetric;

        $this->addMetric($type, $metric, $latestTotal, $latestActive, $increaseTotal, $increaseActive);
    }



    public function projectMetric($duration)
    {
       return $this->getMetric($duration, ProjectMetric::class);
    }

    public function assetMetric($duration)
    {
        // $totalChange = 0;
        // $activeChange = 0;
        // $latestTotal = AssetMetric::whereNotNull("total")->orderBy("created_at", "desc")->first();
        // $latestActive = AssetMetric::whereNotNull("active_total")->orderBy("created_at", "desc")->first();

        // $latestWeekTotal = AssetMetric::where('created_at', '<=', now()->subWeek())->whereNotNull("total")->orderBy("created_at", "desc")->first();
        // $latestWeekActive = AssetMetric::where('created_at', '<=', now()->subWeek())->whereNotNull("active_total")->orderBy("created_at", "desc")->first();
        //         // Add logic to calculate changes based on $latestTotal and $latestActive
        // if($latestWeekTotal) $totalChange = Utilities::calculateAppreciation($latestWeekTotal->total, $latestTotal->total)['percentage'];
        // if($latestWeekActive) $activeChange = Utilities::calculateAppreciation($latestWeekActive->active_total, $latestActive->active_total)['percentage'];
        // dd($latestWeekTotal->total, $latestTotal->total);
        // dd($totalChange);

        return $this->getMetric($duration, AssetMetric::class);
    }

    public function clientMetric($duration)
    {
        // dd($latestActive && $latestActive->created_at->isToday());
        // dd($latestWeekActive);
        // dd(Utilities::calculateAppreciation($latestWeekActive->active_total, $latestWeekActive->previous_active_total)['percentage']);
        
        return $this->getMetric($duration, ClientMetric::class);
    }



    private function addMetric($type, $metric, $latestTotal, $latestActive, $increaseTotal, $increaseActive)
    {
        switch($type) {
            case MetricType::ACTIVE->value : 
                $metric->previous_active_total = $latestActive->active_total;
                $metric->active_total = ($increaseActive) ? $latestActive->active_total++ : $latestActive->active_total--;
                $metric->save();
                break;
            case MetricType::TOTAL->value :
                $metric->previous_total = $latestTotal->total;
                $metric->total = ($increaseTotal) ? $latestTotal->total++ : $latestTotal->total--;
                $metric->save();
                break;
            case MetricType::BOTH->value :
                $metric->previous_total = $latestTotal->total;
                $metric->previous_active_total = $latestActive->active_total;
                $metric->total = ($increaseTotal) ? $latestTotal->total++ : $latestTotal->total--;
                $metric->active_total = ($increaseActive) ? $latestActive->active_total++ : $latestActive->active_total--;
                $metric->save();
                break;
        }
    }

    private function getMetric($duration, $metricClass) 
    {
        $totalChange = 100;
        $activeChange = 100;

        $latestTotal = $metricClass::whereNotNull("total")->orderBy("created_at", "desc")->first();
        $latestActive = $metricClass::whereNotNull("active_total")->orderBy("created_at", "desc")->first();

        switch($duration) {
            case MetricDuration::TODAY->value :
                $totalChange = 0;
                $activeChange = 0;
                if($latestTotal && $latestTotal->created_at->isToday()) $totalChange = Utilities::calculateAppreciation($latestTotal->total, $latestTotal->previous_total)['percentage'];
                if($latestActive && $latestActive->created_at->isToday()) $activeChange = Utilities::calculateAppreciation($latestActive->active_total, $latestTotal->previous_active_total)['percentage'];
                break;
            case MetricDuration::WEEK->value :
                $latestWeekTotal = $metricClass::where('created_at', '<=', now()->subWeek())->whereNotNull("total")->orderBy("created_at", "desc")->first();
                $latestWeekActive = $metricClass::where('created_at', '<=', now()->subWeek())->whereNotNull("active_total")->orderBy("created_at", "desc")->first();
                // Add logic to calculate changes based on $latestTotal and $latestActive
                if($latestWeekTotal) $totalChange = Utilities::calculateAppreciation($latestTotal->total, $latestWeekTotal->total)['percentage'];
                if($latestWeekActive) $activeChange = Utilities::calculateAppreciation($latestActive->active_total, $latestWeekActive->active_total)['percentage'];
                break;
            case MetricDuration::MONTH->value : 
                $latestMonthTotal = $metricClass::where('created_at', '<=', now()->subMonth())->whereNotNull("total")->orderBy("created_at", "desc")->first();
                $latestMonthActive = $metricClass::where('created_at', '<=', now()->subMonth())->whereNotNull("active_total")->orderBy("created_at", "desc")->first();
                // Add logic to calculate changes based on $latestTotal and $latestActive
                if($latestMonthTotal) $totalChange = Utilities::calculateAppreciation($latestTotal->total, $latestMonthTotal->total)['percentage'];
                if($latestMonthActive) $activeChange = Utilities::calculateAppreciation($latestActive->active_total, $latestMonthActive->active_total)['percentage'];
                break;
            case MetricDuration::YEAR->value : 
                $latestYearTotal = $metricClass::where('created_at', '<=', now()->subYear())->whereNotNull("total")->orderBy("created_at", "desc")->first();
                $latestYearActive = $metricClass::where('created_at', '<=', now()->subYear())->whereNotNull("active_total")->orderBy("created_at", "desc")->first();
                // Add logic to calculate changes based on $latestTotal and $latestActive
                if($latestYearTotal) $totalChange = Utilities::calculateAppreciation($latestTotal->total, $latestYearTotal->total)['percentage'];
                if($latestYearActive) $activeChange = Utilities::calculateAppreciation($latestActive->active_total, $latestYearActive->active_total)['percentage'];
                break;
        }

        return ['total' => $totalChange, 'active' => $activeChange];
    }

}
