<?php

namespace app\Services;

use Maatwebsite\Excel\Facades\Excel;
use PDF;

use app\Models\Package;
use app\Models\ClientPackage;
use app\Models\Order;
use app\Models\ClientAssetsView;

class AssetService
{
    public $count = false;
    public $filter = null;

    public function clientAssetSummary($clientId)
    {
        return ClientAssetsView::where("client_id", $clientId)->first();
    }

    public function assetsSummary()
    {
        return ClientAssetsView::all();
    }

    public function assets($with=[], $offset=0, $perPage=null)
    {
        // return ClientPackage::with($with)->get();
        $query = ClientPackage::with($with);
        if($this->filter && is_array($this->filter)) {
            $filter = $this->filter;
            if(isset($filter['text'])) {
                $query->whereHas('package', function($packageQuery) use($filter) {
                    $packageQuery->where("name", "LIKE", "%".$filter['text']."%")
                    ->orWhereHas('project', function($projectQuery) use($filter) {
                        $projectQuery->where("name", "LIKE", "%".$filter['text']."%");
                    });
                })
                ->orWhereHas('client', function($clientQuery) use($filter) {
                    $clientQuery->where("firstname", "LIKE", "%".$filter['text']."%")
                    ->orWhere("lastname", "LIKE", "%".$filter['text']."%");
                });
            }
            if(isset($filter['date'])) $query = $query->whereDate("created_at", $filter['date']);
            if(isset($filter['projectTypeId'])) {
                $query->whereHas('package', function($packageQuery) use($filter) {
                    $packageQuery->whereHas('project', function($projectQuery) use($filter) {
                        $projectQuery->where("project_type_id", $filter['projectTypeId']);
                    });
                });
            }
            if(isset($filter['status'])) $query = ($filter['status'] == 'completed') ? $query->where("purchase_complete", 1) : $query->where("purchase_complete", 0);
        }
        if($this->count) return $query->count();
        return $query->orderBy("created_at", "DESC")->offset($offset)->limit($perPage)->get();
    }

    public function clientAssets($clientId)
    {
        return ClientPackage::where("sold", false)->where("client_id", $clientId)->whereHAs("purchase")->orderBy("created_at", "DESC")->get();
    }
}