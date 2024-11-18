<?php

namespace app\Services;

use Maatwebsite\Excel\Facades\Excel;
use PDF;

use app\Models\Package;
use app\Models\ClientPackage;

use app\Enums\ProjectFilter;

use app\Exports\PackageExport;

class PackageService
{
    public $count = false;

    public function save($data)
    {
        $clientPackage = new ClientPackage;
        $clientPackage->client_id = $data['clientId'];
        $clientPackage->package_id = $data['packageId'];
        $clientPackage->contract_file_id = $data['contractFileId'];
        $clientPackage->happiness_letter_file_id = $data['happinessLetterFileId'];
        $clientPackage->doa_file_id = $data['doaFileId'];
        $clientPackage->origin = $data['origin'];
        $clientPackage->purchase_id = $data['purchaseId'];
        $clientPackage->purchase_type = $data['purchaseType'];
        $clientPackage->save();

        return $clientPackage;
    }


}