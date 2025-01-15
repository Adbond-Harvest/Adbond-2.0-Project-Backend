<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\WalletBankAccountResource;
use app\Http\Resources\WalletResource;
use app\Http\Resources\WalletWithdrawalRequestResource;
use app\Http\Resources\PackageResource;
use app\Http\Resources\ClientInvestmentResource;

use app\Enums\WalletTransactionSource;

class WalletTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = [
            "id" => $this->id,
            "referenceNo" => $this->reference_no,
            "wallet" => new WalletResource($this->whenLoaded("wallet")),
            "bankAccount" => new WalletBankAccountResource($this->whenLoaded("bankAccount")),
            "amount" => $this->amount,
            "balance" => $this->balance,
            "transaction_type" => $this->transaction_type,
            "status" => ($this->confirmed == 1) ? "successful" : (($this->confirmed == 0) ? "failed" : "pending"),
            "withdrawalRequest" => new WalletWithdrawalRequestResource($this->whenLoaded("WithdrawalRequest")),
            "date" => $this->created_at->format('F j, Y')
        ];

        if($this->relationLoaded('source')) $resource['source'] = $this->getSource();

        return $resource;
    }

    public function getSource()
    {
        if($this->source_type == WalletTransactionSource::INVESTMENT->value) return new ClientInvestmentResource($this->source);
        return null;
    }
}
