<?php

namespace app\Services;

use app\Models\AssetDowngrade;
use app\Models\DowngradeUpgradeRequest;
use app\Models\Package;
use app\Models\DeductibleFee;

use app\Services\OrderService;

use app\Enums\PackageType;
use app\Enums\DeductibleFee as DeductibleFeeEnum;
use app\Enums\AssetSwitchType;

use app\Utilities;

class AssetSwitchService
{
    public $count = null;
    public $approved = null;
    public $setStatus = false;

    public function getDownGradePackages($package)
    {
        $projectType = $package->project->projectType;
        return Package::where("amount", "<", $package->amount)->where("type", PackageType::NON_INVESTMENT->value)
            ->whereHas("project", function($projectQuery) use($projectType) {
            $projectQuery->whereHas("projectType", function($projectTypeQuery) use($projectType) {
                $projectTypeQuery->where("id", $projectType->id);
            });
        })->get();
    }

    public function requestAssetSwitch($data)
    {
        $request = new DowngradeUpgradeRequest;
        $request->client_id = $data['clientId'];
        $request->type = $data['type'];
        $request->from_package_id = $data["fromPackageId"];
        $request->to_package_id = $data["toPackageId"];
        $request->client_package_id = $data['assetId'];
        $request->save();

        return $request;
    }

    public function assetSwitchRequests($with=[], $offset=0, $perPage=null)
    {
        $query = DowngradeUpgradeRequest::with($with);
        if($this->setStatus) {
            $query->where("approved", $this->approved);
        }
        if($this->count) return $query->count();

        $query = $query->orderBy("created_at", "DESC");
        if($perPage) $query = $query->limit($perPage);
        return $query->offset($offset)->orderBy("created_at", "Desc")->get();
    }

    public function assetSwitchRequest($id, $with=[])
    {
        return DowngradeUpgradeRequest::with($with)->where("id", $id)->first();
    }

    public function approve($request)
    {
        $request->approved = true;
        $request->update();

        return $request;
    }

    public function reject($request, $reason)
    {
        $request->approved = false;
        $request->rejected_reason = $reason;
        $request->update();

        return $request;
    }

    public function downgrade($request, $toPackage)
    {
        $asset = $request->asset;
        $order = $asset->purchase;
        $clientPackage = $request->asset;
        $fromPackage = $clientPackage->package;

        $fee = DeductibleFee::where("name", DeductibleFeeEnum::DOWNGRADE_PENALTY->value)->first();

        $penalty = Utilities::downgradePenaltyAmount($asset->package, $asset->units, $fee->percentage);
        // add the penalty to the amount of the new package and deduct what has been paid to get the remaining amount
        // $balance = $amountPayable - $order->amount_payed;
        // if($balance < 0) $balance = 0;

        //save downgrade
        $assetDowngrade = new AssetDowngrade;
        $assetDowngrade->client_id = $asset->client_id;
        $assetDowngrade->request_id = $request->id;
        $assetDowngrade->from_package_id = $asset->package_id;
        $assetDowngrade->to_package_id = $request->to_package_id;
        $assetDowngrade->client_package_id = $asset->id;
        $assetDowngrade->penalty = $fee->percentage;
        $assetDowngrade->penalty_amount = $penalty;
        $assetDowngrade->save();

        $amountPayable = ($toPackage->amount * $order->units) + $penalty;
        $balance = $amountPayable - $order->amount_payed;

        $order->package_id = $request->to_package_id;
        $order->amount_payable = $amountPayable;
        $order->unit_price = $toPackage->amount;
        // $order->balance = $balance;
        $order->downgrade_id = $assetDowngrade->id;
        if($balance > 0) $order->completed = false;
        $order->update();

        $clientPackage->package_id = $request->to_package_id;
        $clientPackage->downgrade_id = $assetDowngrade->id;
        $clientPackage->amount = $amountPayable;
        $clientPackage->unit_price = $toPackage->amount;
        if($balance > 0) {
            $clientPackage->purchase_complete = false;
            $clientPackage->purchase_completed_at = null;
        }

        $clientPackage->update();

        //update the packages units count
        $fromPackage->units += $order->units;
        $fromPackage->update();

        $toPackage->units -= $order->units;
        $toPackage->update();

        if($balance == 0) {
            $orderService = new OrderService;
            $orderService->completeDowngradeOrder($order, $clientPackage);
        }

        return $assetDowngrade;
    }
}
