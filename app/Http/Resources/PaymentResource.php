<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\ClientBriefResource;
use app\Http\Resources\OrderResource;
use app\Http\Resources\PaymentGatewayResource;
use app\Http\Resources\PaymentModeResource;
use app\Http\Resources\PaymentStatusResource;
use app\Http\Resources\PaymentPeriodStatusResource;
use app\Http\Resources\BankAccountResource;
use app\Http\Resources\FileResource;

use app\Models\Order;

class PaymentResource extends JsonResource
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
            "reference" => $this->reference,
            "success" => ($this->success == 1),
            "failureMessage" => $this->failure_message,
            "flag" => ($this->flag == 1),
            "purpose" => $this->purpose,
            "paymentMode" => new PaymentModeResource($this->paymentMode),
            "paymentGateway" => new PaymentGatewayResource($this->paymentGateway),
            "paymentEvidence" => new FileResource($this->paymentEvidence),
            "paymentReceipt" => new FileResource($this->paymentReceipt),
            "order" => ($this->purchase_type == Order::$type) ? new OrderResource($this->whenLoaded("order")) : []
        ];
    }

}
