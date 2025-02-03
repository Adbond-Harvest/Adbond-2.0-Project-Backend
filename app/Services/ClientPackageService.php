<?php

namespace app\Services;

use Maatwebsite\Excel\Facades\Excel;
use PDF;

use app\Models\Package;
use app\Models\ClientPackage;
use app\Models\Order;
use app\Models\Offer;
use app\Models\ClientInvestment;
use app\Models\ClientAssetsView;

use app\Services\ClientInvestmentService;

use app\Enums\ProjectFilter;
use app\Enums\ClientPackageOrigin;
use app\Enums\PackageType;
use app\Enums\InvestmentRedemptionOption;

use app\Exports\PackageExport;

class ClientPackageService
{
    public $count = false;

    public function save($data)
    {
        $clientPackage = ClientPackage::where("purchase_id", $data['purchaseId'])->where("purchase_type", $data['purchaseType'])->where("package_id",$data['packageId'])->first();
        if(!$clientPackage) $clientPackage = new ClientPackage;
        $clientPackage->client_id = $data['clientId'];
        $clientPackage->package_id = $data['packageId'];
        if(isset($data['contractFileId'])) $clientPackage->contract_file_id = $data['contractFileId'];
        if(isset($data['happinessLetterFileId'])) $clientPackage->happiness_letter_file_id = $data['happinessLetterFileId'];
        if(isset($data['doaFileId'])) $clientPackage->doa_file_id = $data['doaFileId'];
        $clientPackage->origin = $data['origin'];
        $clientPackage->purchase_id = $data['purchaseId'];
        $clientPackage->purchase_type = $data['purchaseType'];
        $clientPackage->purchase_complete = $data['purchaseComplete'];
        $clientPackage->amount = $data['amount'];
        if(isset($data['units'])) $clientPackage->units = $data['units'];
        $clientPackage->unit_price = $data['unitPrice'];
        $clientPackage->save();

        return $clientPackage;
    }

    public function saveClientPackageOrder($order, $files=[]) {
        $data['clientId'] = $order->client->id;
        $data['packageId'] = $order->package_id;
        $data['origin'] = ClientPackageOrigin::ORDER->value;
        $data['purchaseId'] = $order->id;
        $data['purchaseType'] = Order::$type;
        if(isset($files['contractFileId'])) $data['contractFileId'] = $files['contractFileId'];
        if(isset($files['happinessLetterFileId'])) $data['happinessLetterFileId'] = $files['happinessLetterFileId'];
        $data['purchaseComplete'] = $order->completed;
        $data['packageType'] = $order->package->type;
        $data['amount'] = $order->amount_payable;
        $data['units'] = $order->units;
        $data['unitPrice'] = $order->unit_price;
        $clientPackage = $this->save($data);
        return $clientPackage;
    }

    public function saveClientPackageInvestment($clientInvestment) {
        $data['clientId'] = $clientInvestment->client->id;
        $data['packageId'] = $clientInvestment->package_id;
        $data['origin'] = ClientPackageOrigin::INVESTMENT->value;
        $data['purchaseId'] = $clientInvestment->id;
        $data['purchaseType'] = ClientInvestment::$type;
        $data['purchaseComplete'] = $clientInvestment->order->completed;
        $data['amount'] = $clientInvestment->capital;
        $data['unitPrice'] = $clientInvestment->order->unit_price;

        $clientPackage = $this->save($data);
        return $clientPackage;
    }

    //convert the client package to an asset or delete it in the instance of cash redemption
    public function concludeClientPackageInvestment($clientPackage, $data=null)
    {
        if($clientPackage->purchase_type == ClientInvestment::$type) {
            $clientInvestment = $clientPackage->purchase;
            if( $clientInvestment && $clientInvestment->redemption_option == InvestmentRedemptionOption::CASH->value) {
                $clientPackage->delete();
            }else{
                if($data) {
                    $clientPackage->package_id = $data['packageId'];
                    $clientPackage->amount = $data['amount'];
                    $clientPackage->units = (isset($data['units'])) ? $data['units'] : 1;
                    $clientPackage->unit_price = $data['unitPrice'];
                    if(isset($data['contractFileId'])) $clientPackage->contract_file_id = $data['contractFileId'];
                    if(isset($data['happinessLetterFileId'])) $clientPackage->happiness_letter_file_id = $data['happinessLetterFileId'];
                    $clientPackage->update();
                }
            }
            
        }
    }

    public function update($data, $clientPackage)
    {
        if(isset($data['contractFileId'])) $clientPackage->contract_file_id = $data['contractFileId'];
        if(isset($data['happinessLetterFileId'])) $clientPackage->happiness_letter_file_id = $data['happinessLetterFileId'];
        if(isset($data['doaFileId'])) $clientPackage->doa_file_id = $data['doaFileId'];
        $clientPackage->update();
        return $clientPackage;
    }

    public function clientPackage($id, $with=[])
    {
        return ClientPackage::with($with)->where("id", $id)->first();
    }

    public function clientAssetSummary($clientId)
    {
        return ClientAssetsView::where("client_id", $clientId)->first();
    }

    public function clientAssets($clientId)
    {
        return ClientPackage::where("client_id", $clientId)->get();
    }

}