<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use DateTime;

use app\Services\ClientInvestmentService;
use app\Services\WalletService;
use app\Services\FileService;
use app\Services\ClientPackageService;

use app\Enums\InvestmentRedemptionOption;

use app\Helpers;
use app\Utilities;

class CronJobController extends Controller
{
    private $clientInvestmentService;
    private $walletService;
    private $fileService;
    private $clientPackageService;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->clientInvestmentService = new ClientInvestmentService;
        $this->walletService = new WalletService;
        $this->fileService = new FileService;
        $this->clientPackageService = new ClientPackageService;
    }

    public function checkInvestmentReturns()
    {
        try{
            DB::beginTransaction();
            Utilities::logStuff("running this");
            $clientInvestmentService = new ClientInvestmentService;
            $walletService = new WalletService;
            // get all ongoing client investments
            $investments = $clientInvestmentService->runningInvestments();

            $interestPaidOptions = [InvestmentRedemptionOption::CASH->value, InvestmentRedemptionOption::PROFIT_ONLY->value];
            $propertyOptions = [InvestmentRedemptionOption::PROFIT_ONLY->value, InvestmentRedemptionOption::PROPERTY->value];

            // Loop through all of the investments if they exists
            if($investments->count() > 0) {
                Utilities::logStuff("loop through running investments");
                foreach($investments as $investment) {
                    $wallet = $this->walletService->clientWallet($investment->client->id);
                    //check if the end-date has reached or passed, if so, end the investment
                    if(Carbon::parse($investment->end_date)->isToday() || \Carbon\Carbon::parse($investment->end_date)->isPast()) {
                        $this->endInvestment($investment, $wallet);
                    }else{
                        // if end-date has not reached, and if the redemption type is profit-only or cash, 
                        if( in_array($investment->redemption_option, $interestPaidOptions) ) {
                            Utilities::logStuff("profit only");
                            // check if the next return has reached or passed
                            if(Carbon::parse($investment->next_interest_date)->isToday() || \Carbon\Carbon::parse($investment->next_interest_date)->isPast()) {
                                $amount = $this->clientInvestmentService->getProfit($investment);
                                Utilities::logStuff("gotten profit");
                                if($wallet) {
                                    Utilities::logStuff("wallet exists");
                                    $this->walletService->creditInvestmentProfit($wallet, $investment, $amount);
                                    $investment->interest_payments_left -= 1;
                                    $investment->next_interest_date =  (new DateTime($investment->next_interest_date))->modify('+'.$investment->duration.' months')->format('Y-m-d');
                                    $investment->update();
                                    Utilities::logStuff("updated");
                                }
                            }else{
                                Utilities::logStuff("not time");
                            }
                        }else{
                            Utilities::logStuff("not profit only");
                        }
                    }

                        // Process the returns by adding the profit to the wallet
                }
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            Utilities::logStuff('Error Checking investment returns: '.$e->getMessage().' in '.$e->getFile().' at Line '.$e->getLine());
        }
    }

    private function endInvestment($investment, $wallet)
    {
        $interestPaidOptions = [InvestmentRedemptionOption::CASH->value, InvestmentRedemptionOption::PROFIT_ONLY->value];
        $propertyOptions = [InvestmentRedemptionOption::PROFIT_ONLY->value, InvestmentRedemptionOption::PROPERTY->value];
        if( in_array($investment->redemption_option, $interestPaidOptions) ) {
            if($investment->interest_payments_left > 0) {
                // get The interest
                $amount = $this->clientInvestmentService->getProfit($investment);
                if($wallet) {
                    $this->walletService->creditInvestmentProfit($wallet, $investment, $amount, true);
                    $investment->interest_payments_left = 0;
                    $investment->ended = true;
                    $investment->update();
                }
            }
        }
        $data = null;
        if( in_array($investment->redemption_options, $propertyOptions) ) {
            $contractFileId = $this->handleContract($investment);

            $data = [
                "packageId" => $investment->redemption_package_id,
                "amount" => $investment->redemptionPackage->amount,
                "unitPrice" => $investment->redemptionPackage->unit_price,
                "contractFileId" => $contractFileId
            ];
        }
        $this->clientPackageService->concludeClientPackageInvestment($investment->clientPackage, $data);
    }

    private function prepareContract($investment)
    {
        // $itemPrice = Helpers::item_price($order->packageItem);
        $data['package'] = $investment?->redemptionPackage?->name;
        $data['project'] = $investment?->redemptionPackage?->project?->name;
        $data['client'] =  ucfirst($investment->client->full_name);
        $data['address'] = $investment->client->address;
        // $data['location'] = $investment?->redemptionPackageItem?->package?->project_location?->location?->name;
        $projectAddress = $investment?->redemptionPackage?->address;
        $state = $investment?->redemptionPackage?->state.' State';
        $data['location'] = ($projectAddress) ? $projectAddress.', '.$state : $state;
        $data['state'] = $state;
        $data['price'] = $investment->redemptionPackage->amount;
        $data['installment'] = false;
        $data['installment_duration'] = '-';
        // if the units ordered is more than 1, multiply by units
        // if($order->units && $order->units > 1) $data['price'] = $data['price'] * $order->units; 
        $data['size'] = $investment?->redemptionPackage?->size;
        // Helpers::wordDoc($data);
        // return Helpers::generateContract($data);
        return $data;
        // dd($data);
    }

    private function handleContract($investment)
    {
        try{
            $clientPackage = $investment->clientPackage;
            $preparedData = $this->prepareContract($investment);
            Helpers::generateContract($investment->order, $preparedData);

            $uploadedContract = "files/contract_{$investment->order->id}.pdf";
            
            $response = Helpers::moveUploadedFileToCloud($uploadedContract, FileTypes::PDF->value, $investment->client->id, 
            FilePurpose::CONTRACT->value, "app\Models\Client", "client-contracts");
            
            if($response['success']) {
                $contractFileId = $response['upload']['file']->id;
                $contractFileObj = $response['upload']['file'];

                $fileMeta = ["belongsId"=>$clientPackage->id, "belongsType"=>"app\Models\ClientPackage"];
                if($contractFileObj) $this->fileService->updateFileObj($fileMeta, $contractFileObj);
                return $contractFileId;
            }
            return null;
        }catch(\Exception $e){
            Utilities::logStuff('Error Handling Contract: '.$e->getMessage().' in '.$e->getFile().' at Line '.$e->getLine());
        }
        return null;
    }
}
