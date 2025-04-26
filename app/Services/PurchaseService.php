<?php

namespace app\Services;

use app\Models\ClientPurchasesSummaryView;
use app\Models\ClientPackage;

use app\Enums\PurchaseSummaryDuration;

class PurchaseService
{
    public $summaryDuration = PurchaseSummaryDuration::WEEK->value;
    public $start = null;
    public $end = null;

    public function clientPurchaseSummary()
    {
        $start = null;
        $end = null;
        switch($this->summaryDuration) {
            case PurchaseSummaryDuration::WEEK->value : 
                $start = now()->startOfWeek();
                $end = now()->endOfWeek();
                break;
            case PurchaseSummaryDuration::MONTH->value :
                $start = now()->startOfMonth();
                $end = now()->endOfMonth();
                break;
            case PurchaseSummaryDuration::YEAR->value :
                $start = now()->startOfYear();
                $end = now()->endOfYear();
                break;
            case PurchaseSummaryDuration::CUSTOM->value :
                $start = $this->start;
                $end = $this->end;
        }
        // if($start) $start = explode(' ', $start)[0];
        // if($end) $end = explode(' ', $end)[0];
        // dd($start.' - '.$end);
        if($this->summaryDuration == PurchaseSummaryDuration::TODAY->value) {
            // return ClientPackage::select()
            return ClientPackage::select('amount as total_amount', 'purchase_completed_at as purchase_date')
                ->where('origin', 'order')
                ->whereDate('purchase_date', now())
                ->get();
        }
        $query = ClientPurchasesSummaryView::query();
        if($start && $end) {
            $query = $query->whereBetween('purchase_date', [$start, $end]);
        }else{
            if($start) $query = $query->where("purchase_date", ">=", $start);
            if($end) $query = $query->where("purchase_date", "<=", $end);
        }
        if($this->summaryDuration == PurchaseSummaryDuration::ALL->value || (!$start && !$end)) {
            return ClientPurchasesSummaryView::all();
        }

        return $query->get();
    }
}