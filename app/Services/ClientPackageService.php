<?php

namespace app\Services;

use Maatwebsite\Excel\Facades\Excel;
use PDF;

use app\Models\Package;
use app\Models\ClientPackage;
use app\Models\Order;
use app\Models\Offer;

use app\Enums\ProjectFilter;
use app\Enums\ClientPackageOrigin;

use app\Exports\PackageExport;

class ClientPackageService
{
    public $count = false;

    public function save($data)
    {
        $clientPackage = new ClientPackage;
        $clientPackage->client_id = $data['clientId'];
        $clientPackage->package_id = $data['packageId'];
        if(isset($data['contractFileId'])) $clientPackage->contract_file_id = $data['contractFileId'];
        if(isset($data['happinessLetterFileId'])) $clientPackage->happiness_letter_file_id = $data['happinessLetterFileId'];
        if(isset($data['doaFileId'])) $clientPackage->doa_file_id = $data['doaFileId'];
        $clientPackage->origin = $data['origin'];
        $clientPackage->purchase_id = $data['purchaseId'];
        $clientPackage->purchase_type = $data['purchaseType'];
        $clientPackage->save();

        return $clientPackage;
    }

    public function saveClientPackageOrder($order, $files=[]) {
        $data['clientId'] = $order->client->id;
        $data['packageId'] = $order->package_id;
        $data['origin'] = ClientPackageOrigin::ORDER->value;
        $data['purchaseId'] = $order->id;
        $data['purchaseType'] = Order::$type;
        if($files['contractFileId']) $data['contractFileId'] = $files['contractFileId'];
        if($files['letterOfHappinessFileId']) $data['happinessLetterFileId'] = $files['letterOfHappinessFileId'];
        $clientPackage = $this->save($data);
        return $clientPackage;
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


}