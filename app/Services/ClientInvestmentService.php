<?php

namespace app\Services;

use DateTime;

use app\Models\ClientInvestment;

use app\Utilities;

class ClientInvestmentService
{

    public function getByOrderId($orderId)
    {
        return ClientInvestment::where("order_id", $orderId)->first();
    }

    public function runningInvestments()
    {
        return ClientInvestment::where("started", 1)->where("ended", 0)->get();
    }

    public function save($data)
    {
        $clientInvestment = ClientInvestment::where("order_id", $data['orderId'])->first();
        if(!$clientInvestment) $clientInvestment = new ClientInvestment;
        $clientInvestment->client_id = $data['clientId'];
        $clientInvestment->package_id = $data['packageId'];
        $clientInvestment->order_id = $data['orderId'];
        $clientInvestment->redemption_option = $data['redemptionOption'];
        if(isset($data['redemptionPackageId'])) $clientInvestment->redemption_package_id = $data['redemptionPackageId'];
        $clientInvestment->capital = $data['capital'];
        $clientInvestment->duration = $data['duration'];
        $clientInvestment->timeline = $data['timeline'];
        $clientInvestment->interest_payments_left = ceil($data['timeline']/$data['duration']);
        if(isset($data['percentage'])) $clientInvestment->percentage = $data['percentage'];
        if(isset($data['amount'])) $clientInvestment->amount = $data['amount'];
        if(isset($data['startDate'])) {
            $clientInvestment->start_date = $data['startDate'];
            $clientInvestment->next_interest_date = (new DateTime($data['startDate']))->modify('+'.$data['duration'].' months')->format('Y-m-d');
            $clientInvestment->started = true;
        }
        if(isset($data['endDate'])) {
            $clientInvestment->end_date = $data['endDate'];
        }

        $clientInvestment->save();

        return $clientInvestment;
    }

    public function start($investment)
    {
        $investment->start_date = Date("Y-m-d");
        $investment->next_interest_date = (new DateTime($investment->start_date))->modify('+'.$investment->duration.' months')->format('Y-m-d');
        $investment->started = true;
        $investment->end_date = (new DateTime($investment->start_date))->modify('+'.$investment->timeline.' months')->format('Y-m-d');
        $investment->update();
        return $investment;
    }

    public function saveInvestment($order, $data)
    {
        $investmentData['clientId'] = $order->client_id;
        $investmentData['packageId'] = $order->package_id;
        $investmentData['orderId'] = $order->id;
        $investmentData['redemptionOption'] = $data['redemptionOption'];
        if(isset($data['redemptionPackageId'])) $investmentData['redemptionPackageId'] = $data['redemptionPackageId'];
        $investmentData['capital'] = $order->amount_payable;
        $investmentData['duration'] = $order->package->interest_return_duration;
        $investmentData['timeline'] = $order->package->interest_return_timeline;
        if($order->package->interest_return_percentage) $investmentData['percentage'] = $order->package->interest_return_percentage;
        if($order->package->interest_return_amount) $investmentData['amount'] = $order->package->interest_return_amount; 
        if($order->completed == 1) {
            $investmentData['startDate'] = Date("Y-m-d");
            $investmentData['started'] = true;
            $investmentData['endDate'] = (new DateTime($investmentData['startDate']))->modify('+'.$investmentData['timeline'].' months')->format('Y-m-d');
        }
        $clientInvestment = $this->save($investmentData);

        return $clientInvestment;
    }

    public function addMemorandumAgreement($memorandumAgreementFileId, $clientInvestment)
    {
        $clientInvestment->memorandum_agreement_file_id = $memorandumAgreementFileId;
        $clientInvestment->update();
        return $clientInvestment;
    }

    public function getProfit($clientInvestment)
    {
        if($clientInvestment->amount) {
            return $clientInvestment->amount;
        }
        return Utilities::getPercentageAmount($clientInvestment->capital, $clientInvestment->percentage);
    }

    public function endInvestment($clientInvestment)
    {
        $amount = $this->getProfit($clientInvestment);
        $clientInvestment->ended = true;
        $clientInvestment->updated();

        return ["amount"=>$amount, "investment"=>$clientInvestment];
    }
}