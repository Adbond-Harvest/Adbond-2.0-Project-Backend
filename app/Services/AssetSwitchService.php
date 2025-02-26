<?php

namespace app\Services;

use app\Models\AssetDowngrade;
use app\Models\DowngradeUpgradeRequest;
use app\Models\Package;
use app\Models\DeductibleFee;

use app\Services\OrderService;

use app\Enums\PackageType;
use app\Enums\DeductibleFee as DeductibleFeeEnum;

use app\Utilities;

class AssetSwitchService
{
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

    public function approve($request)
    {
        $request->approved = true;
        $request->update();

        $fee = DeductibleFee::where("name", DeductibleFeeEnum::DOWNGRADE_PENALTY->value)->first();

        $order = $request->asset->purchase;
        $penalty = Utilities::downgradePenaltyAmount($request->fromPackage, $request->asset->units, $fee->percentage);
        // add the penalty to the amount of the new package and deduct what has been paid to get the remaining amount
        $amountPayable = ($request->toPackage->amount * $order->units) + $penalty;
        $balance = $amountPayable - $order->amount_payed;
        if($balance < 0) $balance = 0;

        //save downgrade
        $assetDowngrade = new AssetDowngrade;
        $assetDowngrade->client_id = $request->client_id;
        $assetDowngrade->request_id = $request->id;
        $assetDowngrade->from_package_id = $request->from_package_id;
        $assetDowngrade->to_package_id = $request->to_package_id;
        $assetDowngrade->client_package_id = $request->client_package_id;
        $assetDowngrade->penalty = $fee->percentage;
        $assetDowngrade->penalty_amount = $penalty;
        $assetDowngrade->save();

        //update order
        $order->package_id = $request->to_package_id;
        $order->amount_payable = $amountPayable;
        $order->balance = $balance;
        $order->downgrade_id = $assetDowngrade->id;
        $order->update();

        $clientPackage = $request->asset;
        $clientPackage->package_id = $request->to_package_id;
        $clientPackage->downgrade_id = $assetDowngrade->id;
        $clientPackage->update();

        //if the balance is zero, complete the order
        if($balance == 0) {
            $orderService = new OrderService;
            $orderService->completeDowngradeOrder($order, $clientPackage);
        }

        return $request;
    }

    public function reject($request, $reason)
    {
        $request->approved = false;
        $request->rejected_reason = $reason;
        $request->update();

        return $request;
    }
}
