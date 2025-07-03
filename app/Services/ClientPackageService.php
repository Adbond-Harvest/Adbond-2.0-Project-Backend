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

use app\Mail\LetterOfHappiness;
use app\Mail\Contract;

use app\Services\ClientInvestmentService;

use app\Enums\ProjectFilter;
use app\Enums\ClientPackageOrigin;
use app\Enums\PackageType;
use app\Enums\FilePurpose;
use app\Enums\InvestmentRedemptionOption;

use app\Exports\PackageExport;

use app\Services\MetricService;

use app\Enums\MetricType;

use app\Helpers;

class ClientPackageService
{
    public $count = false;
    public $filter = null;
    public $active = null;

    // This method either saves or updates client package
    public function save($data, $clientPackage=null)
    {
        if(!$clientPackage) {
            $clientPackage = ClientPackage::where("purchase_id", $data['purchaseId'])->where("purchase_type", $data['purchaseType'])->where("package_id",$data['packageId'])->first();
            if(!$clientPackage) $clientPackage = new ClientPackage;
        }
        $clientPackage->client_id = $data['clientId'];
        $clientPackage->package_id = $data['packageId'];
        if(isset($data['contractFileId'])) $clientPackage->contract_file_id = $data['contractFileId'];
        if(isset($data['happinessLetterFileId'])) $clientPackage->happiness_letter_file_id = $data['happinessLetterFileId'];
        if(isset($data['doaFileId'])) $clientPackage->doa_file_id = $data['doaFileId'];
        $clientPackage->origin = $data['origin'];
        $clientPackage->purchase_id = $data['purchaseId'];
        $clientPackage->purchase_type = $data['purchaseType'];
        if(isset($data['purchaseComplete'])) $clientPackage->purchase_complete = $data['purchaseComplete'];
        $clientPackage->amount = $data['amount'];
        if(isset($data['units'])) $clientPackage->units = $data['units'];
        $clientPackage->unit_price = $data['unitPrice'];
        if($clientPackage->purchase_complete == 1) $clientPackage->purchase_completed_at = now();
        $clientPackage->save();

        return $clientPackage;
    }

    public function saveClientPackageOrder($order, $files=[], $clientPackage=null) {
        $data['clientId'] = $order->client->id;
        $data['packageId'] = $order->package_id;
        $data['origin'] = ClientPackageOrigin::ORDER->value;
        $data['purchaseId'] = $order->id;
        $data['purchaseType'] = Order::$type;
        if(isset($files['contractFileId'])) $data['contractFileId'] = $files['contractFileId'];
        if(isset($files['happinessLetterFileId'])) $data['happinessLetterFileId'] = $files['happinessLetterFileId'];
        if($order->completed == 1) $data['purchaseComplete'] = true;
        $data['packageType'] = $order->package->type;
        $data['amount'] = $order->amount_payable;
        $data['units'] = $order->units;
        $data['unitPrice'] = $order->unit_price;
        $clientPackage = (!$clientPackage) ? $this->save($data) : $this->save($data, $clientPackage);

        // Add Asset Metric;
        $metricService = new MetricService;

        ($order->is_installment == 1) ? $metricService->addAssetMetric(MetricType::BOTH->value) : $metricService->addAssetMetric(MetricType::TOTAL->value);
        
        return $clientPackage;
    }

    public function saveClientPackageOffer($offer, $files=[]) {
        $data['clientId'] = $offer->acceptedBid->client_id;
        $data['packageId'] = $offer->package_id;
        $data['origin'] = ClientPackageOrigin::OFFER->value;
        $data['purchaseId'] = $offer->id;
        $data['purchaseType'] = Offer::$type;
        if(isset($files['contractFileId'])) $data['contractFileId'] = $files['contractFileId'];
        if(isset($files['happinessLetterFileId'])) $data['happinessLetterFileId'] = $files['happinessLetterFileId'];
        $data['purchaseComplete'] = true;
        $data['amount'] = $offer->acceptedBid->price;
        $data['units'] = $offer->units;
        $data['unitPrice'] = $offer->package->amount;
        $clientPackage = $this->save($data);
        return $clientPackage;
    }

    public function saveClientPackageInvestment($clientInvestment) {
        $data['clientId'] = $clientInvestment->client->id;
        $data['packageId'] = $clientInvestment->package_id;
        $data['origin'] = ClientPackageOrigin::INVESTMENT->value;
        $data['purchaseId'] = $clientInvestment->id;
        $data['purchaseType'] = ClientInvestment::$type;
        if($clientInvestment->order->completed == 1) $data['purchaseComplete'] = true;
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

    public function markAsSold($clientPackage)
    {
        $clientPackage->sold = true;
        $clientPackage->update();
    }

    public function uploadLetterOfHappiness($payment, $asset)
    {
        // generate letter of happiness if the card payment was successful
        try{
            $fileService = new FileService;
            $uploadedFile = ($payment->purchase->package->project->project_type_id == ProjectType::land()->id) ?
                    Helpers::generateLetterOfHappiness($payment->load('paymentMode')) : Helpers::generateHomesLetterOfHappiness($payment->load('paymentMode'));
            // dd('generate letter of happiness');
            $response = Helpers::moveUploadedFileToCloud($uploadedFile, FileTypes::PDF->value, $asset->client->id, 
                                FilePurpose::LETTER_OF_HAPPINESS->value, UserType::CLIENT->value, "client-letter-of-happiness");
            if($response['success']) {
                $fileMeta = ["belongsId"=>$asset->id, "belongsType"=>ClientPackage::$type];
                $fileService->updateFileObj($fileMeta, $response['upload']['file']);

                $this->update(['happinessLetterFileId' => $response['upload']['file']->id], $asset);
                // dd("got here");
                try{
                    // Send Letter of Happiness Mail
                    Mail::to($payment->client->email)->send(new LetterOfHappiness($asset->client, $uploadedFile));
                    unlink($response['path']);
                }catch(\Exception $e) {
                    Utilities::logStuff("Error Occurred while attempting to send Letter of Happiness Email..".$e);
                }
            }
        }catch(\Exception $e) {
            Utilities::logStuff("Error Occurred while attempting to generate and upload letter of Happiness..".$e);
        }
    }

    public function uploadContract($order, $asset)
    {
        // generate Contract
        try{
            $fileService = new FileService;
            $uploadedFile = Helpers::generateContract($order);
            // dd('generate Contract');
            $response = Helpers::moveUploadedFileToCloud($uploadedFile, FileTypes::PDF->value, $asset->client->id, 
                                FilePurpose::CONTRACT->value, UserType::CLIENT->value, "client-contracts");
            if($response['success']) {
                $fileMeta = ["belongsId"=>$asset->id, "belongsType"=>ClientPackage::$type];
                $fileService->updateFileObj($fileMeta, $response['upload']['file']);

                $this->update(['contractFileId' => $response['upload']['file']->id], $asset);
                // dd("got here");
                try{
                    // Send Contract Mail
                    Mail::to($asset->client->email)->send(new Contract($asset->client, $uploadedFile));
                    unlink($response['path']);
                }catch(\Exception $e) {
                    Utilities::logStuff("Error Occurred while attempting to send Contract Email..".$e);
                }
            }
        }catch(\Exception $e) {
            Utilities::logStuff("Error Occurred while attempting to generate and upload Contract..".$e);
        }
    }

    public function clientPackage($id, $with=[])
    {
        return ClientPackage::with($with)->where("id", $id)->first();
    }

    public function clientAssetSummary($clientId)
    {
        return ClientAssetsView::where("client_id", $clientId)->first();
    }

    public function clientAssets($clientId, $with=[], $offset=0, $perPage=null)
    {
        // return ClientPackage::where("client_id", $clientId)->get();
        $query = ClientPackage::with($with)->where("client_id", $clientId);
        if($this->filter && is_array($this->filter)) {
            $filter = $this->filter;
            if(isset($filter['text'])) {
                $query->whereHas('package', function($packageQuery) use($filter) {
                    $packageQuery->where("name", "LIKE", "%".$filter['text']."%")
                    ->orWhereHas('project', function($projectQuery) use($filter) {
                        $projectQuery->where("name", "LIKE", "%".$filter['text']."%");
                    });
                });
            }
            if(isset($filter['date'])) $query = $query->whereDate("created_at", $filter['date']);
        }
        if($this->count) return $query->count();
        return $query->orderBy("created_at", "DESC")->offset($offset)->limit($perPage)->get();
    }

    public function assets($with=[], $offset=0, $perPage=null)
    {
        $query = ClientPackage::with($with);
        if($this->active !== null) {
            $query = ($this->active) ? $query->where("purchase_complete", 0) : $query->where("purchase_complete", 1);
        }
        if($this->filter && is_array($this->filter)) {
            $filter = $this->filter;
            if(isset($filter['text'])) {
                $query->whereHas('package', function($packageQuery) use($filter) {
                    $packageQuery->where("name", "LIKE", "%".$filter['text']."%")
                    ->orWhereHas('project', function($projectQuery) use($filter) {
                        $projectQuery->where("name", "LIKE", "%".$filter['text']."%");
                    });
                });
            }
            if(isset($filter['date'])) $query = $query->whereDate("created_at", $filter['date']);
        }
        if($this->count) return $query->count();
        return $query->orderBy("created_at", "DESC")->offset($offset);
        return ($perPage) ? $query->limit($perPage)->get() : $query->get();
    }

}