<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\OfferResource;

class OfferPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "receiptNumber" => $this->receipt_no,
            "amount" => $this->amount,
            "confirmed" => ($this->confirmed == 1),
            "rejectedMessage" => $this->rejected_message,
            "reference" => $this->reference,
            "success" => ($this->success == 1),
            "failureMessage" => $this->failure_message,
            "flag" => ($this->flag == 1),
            "flagMessage" => $this->flag_message,
            "purpose" => $this->purpose,
            "paymentMode" => new PaymentModeResource($this->paymentMode),
            "paymentGateway" => new PaymentGatewayResource($this->paymentGateway),
            "paymentEvidence" => new FileResource($this->paymentEvidence),
            "paymentReceipt" => new FileResource($this->paymentReceipt),
            "offer" => new OfferResource($this->whenLoaded("purchase"))
        ];
    }
}
