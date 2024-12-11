<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\WalletBankAccountResource;
use app\Http\Resources\WalletResource;
use app\Http\Resources\WalletWithdrawalRequestResource;
use app\Http\Resources\PackageResource;

class WalletTransactionResource extends JsonResource
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
            "wallet" => new WalletResource($this->whenLoaded("wallet")),
            "bankAccount" => new WalletBankAccountResource($this->whenLoaded("bankAccount")),
            "amount" => $this->amount,
            "balance" => $this->balance,
            "transaction_type" => $this->transaction_type,
            "package" => new PackageResource($this->whenLoaded("package")),
            "status" => ($this->confirmed == 1) ? "confirmed" : (($this->confirmed == 0) ? "declined" : "pending"),
            "withdrawalRequest" => new WalletWithdrawalRequestResource($this->whenLoaded("WithdrawalRequest"))
        ];
    }
}
