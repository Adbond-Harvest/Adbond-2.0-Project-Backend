<?php

namespace app\Services;

use app\Models\Payment;
use app\Models\Offer;
use app\Models\OfferBid;

use app\Enums\UserType;
use app\Enums\FileTypes;use app\Enums\FilePurpose;


use app\Services\FileService;

use app\Helpers;
use app\Utilities;

class OfferPaymentService
{
    public function save($data)
    {
        $payment = new Payment;
        $payment->purchase_id = $data['purchaseId'];
        $payment->purchase_type = $data['purchaseType'];
        $payment->client_id = $data['clientId'];
        $payment->amount = $data['amount'];
        $payment->payment_mode_id = $data['paymentModeId'];
        if(isset($data['confirmed'])) $payment->confirmed = $data['confirmed'];
        
        if(isset($data['evidenceFileId'])) $payment->evidence_file_id = $data['evidenceFileId'];
        if(isset($data['paymentGatewayId'])) $payment->payment_gateway_id = $data['paymentGatewayId'];
        if(isset($data['reference'])) $payment->reference = $data['reference'];
        if(isset($data['success'])) $payment->success = $data['success'];
        if(isset($data['failureMessage'])) $payment->failure_message = $data['failureMessage'];
        if(isset($data['flag'])) $payment->flag = $data['flag'];
        if(isset($data['flagMessage'])) $payment->flag_message = $data['flagMessage'];
        if(isset($data['bankAccountId'])) $payment->bank_account_id = $data['bankAccountId'];
        if(isset($data['paymentDate'])) $payment->payment_date = $data['paymentDate'];
        if(isset($data['receiptFileId'])) $payment->receipt_file_id = $data['receiptFileId'];
        if(isset($data['receiptNumber'])) $payment->receipt_no = $data['receiptNumber'];
        $payment->purpose = $data['purpose'];
        if(isset($data['userId'])) $payment->user_id = $data['userId'];

        $payment->save();
        return $payment;
    }
}