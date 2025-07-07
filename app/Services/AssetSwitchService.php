<?php

namespace app\Services;

use app\Models\AssetDowngrade;
use app\Models\AssetUpgrade;
use app\Models\DowngradeUpgradeRequest;
use app\Models\Package;
use app\Models\DeductibleFee;
use app\Models\Order;
use app\Models\PaymentStatus;
use app\Models\PaymentPeriodStatus;
use app\Models\ClientPackage;

use app\Services\OrderService;

use app\Enums\PackageType;
use app\Enums\DeductibleFee as DeductibleFeeEnum;
use app\Enums\AssetSwitchType;
use app\Enums\UpgradeType;
use app\Enums\OrderType;
use app\Enums\ClientPackageOrigin;

use app\Utilities;

class AssetSwitchService
{
    public $count = null;
    public $approved = null;
    public $setStatus = false;

    public function getDownGradePackages($package, $ids=false)
    {
        $projectType = $package->project->projectType;
        $query = Package::where("amount", "<", $package->amount)->where("type", PackageType::NON_INVESTMENT->value)
            ->whereHas("project", function($projectQuery) use($package) {
                $projectQuery->where("id", $package?->project?->id);
            // $projectQuery->whereHas("projectType", function($projectTypeQuery) use($projectType) {
            //     $projectTypeQuery->where("id", $projectType->id);
            // });
        });
        return ($ids===false) ? $query->get() : $query->pluck("id")->toArray();
    }

    public function getUpGradePackages($package, $ids=false)
    {
        // dd($package->amount);
        $projectType = $package->project->projectType;
        $query = Package::where("amount", ">", $package->amount)->where("type", PackageType::NON_INVESTMENT->value)
            ->whereHas("project", function($projectQuery) use($package) {
                $projectQuery->where("id", $package?->project?->id);
            // $projectQuery->whereHas("projectType", function($projectTypeQuery) use($projectType) {
            //     $projectTypeQuery->where("id", $projectType->id);
            // });
        });
        return ($ids===false) ? $query->get() : $query->pluck("id")->toArray();
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

    public function upgrade($request, $toPackage)
    {
        $asset = $request->asset;
        $order = $asset->purchase;
        $fromPackage = $asset->package;

        $assetUpgrade = new AssetUpgrade;
        $assetUpgrade->type = ($asset->purchase_complete == 0) ? UpgradeType::ORDER->value : UpgradeType::ASSET->value;
        $assetUpgrade->client_id = $request->client_id;
        $assetUpgrade->request_id = $request->id;
        $assetUpgrade->from_package_id = $asset->package_id;
        $assetUpgrade->to_package_id = $toPackage->id;
        $assetUpgrade->client_package_id = $asset->id;
        $assetUpgrade->save();

        if($asset->purchase_complete == 0) {
            $amountPayable = $toPackage->amount * $asset->units;
            $balance = $amountPayable - $order->amount_payed;

            $order->package_id = $request->to_package_id;
            $order->amount_payable = $amountPayable;
            $order->unit_price = $toPackage->amount;
            $order->upgrade_id = $assetUpgrade->id;
            $order->update();

            $asset->package_id = $request->to_package_id;
            $asset->upgrade_id = $assetUpgrade->id;
            $asset->amount = $amountPayable;
            $asset->unit_price = $toPackage->amount;
            if($balance > 0) {
                $asset->purchase_complete = false;
                $asset->purchase_completed_at = null;
            }

            $asset->update();
        }else{
            $order = new Order;
            $order->type = OrderType::UPGRADE->value;
            $order->client_id = $request->client_id;
            $order->package_id = $toPackage->id;
            $order->units = $asset->units;
            $order->amount_payed = 0;
            $order->amount_payable = ($toPackage->amount * $toPackage->units) - $asset->amount;
            $order->unit_price = $toPackage->amount;
            $order->payment_status_id = PaymentStatus::pending()->id;
            $order->order_date = now();
            $order->payment_period_status_id = PaymentPeriodStatus::normal()->id;
            $order->save();

            $assetUpgrade->order_id = $order->id;
            $assetUpgrade->update();

            $asset->upgrade_id = $assetUpgrade->id;
            $asset->update();

            $newAsset = new ClientPackage;
            $newAsset->client_id = $request->client_id;
            $newAsset->package_id = $toPackage->id;
            $newAsset->amount = $toPackage->amount * $asset->units;
            $newAsset->units = $asset->units;
            $newAsset->unit_price = $toPackage->amount;
            $newAsset->origin = ClientPackageOrigin::ORDER->value;
            $newAsset->purchase_type = Order::$type;
            $newAsset->purchase_id = $order->id;
            $newAsset->upgrade_id = $assetUpgrade->id;
            $newAsset->save();

        }

        $fromPackage->units += $order->units;
        $fromPackage->update();

        $toPackage->units -= $order->units;
        $toPackage->update();

        return $assetUpgrade;
    }
}
