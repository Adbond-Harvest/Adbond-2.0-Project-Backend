<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\WalletBankAccountResource;
use app\Http\Resources\WalletTransactionResource;
use app\Http\Resources\WalletWithdrawalRequestResource;
use app\Http\Resources\ClientBriefResource;

use app\Services\WalletService;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $walletService = new WalletService;
        return [
            "id" => $this->id,
            "client" => new ClientBriefResource($this->whenLoaded("client")),
            "lockedAmount" => $this->locked_amount,
            "availableAmount" => $this->amount - $this->locked_amount,
            "currentBalance" => $this->amount,
            "totalBalance" => $this->total,
            "totalOutflow" => $walletService->totalOutflows($this->client),
            "transactionPinSet" => ($this->transaction_pin) ? true : false,
            "bankAccounts" => WalletBankAccountResource::collection($this->whenLoaded("bankAccounts")),
            "transactions" => WalletTransactionResource::collection($this->whenLoaded("transactions")),
            "withdrawalRequests" => WalletWithdrawalRequestResource::collection($this->whenLoaded("withdrawalRequests"))
        ];
    }

}
