<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\WalletBankAccountResource;
use app\Http\Resources\WalletResource;
use app\Http\Resources\ClientBriefResource;
use app\Http\Resources\UserBriefResource;

class WalletWithdrawalRequestResource extends JsonResource
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
            "referenceNo" => $this->reference_no,
            "amount" => $this->amount,
            "walletBalance" => $this->wallet->balance,
            "client" => new ClientBriefResource($this->whenLoaded("wallet.client")),
            "wallet" => new WalletResource($this->whenLoaded("wallet")),
            "status" => $this->status,
            "rejectedReason" => $this->rejected_reason,
            "treatedBy" => new UserBriefResource($this->whenLoaded("user")),
            "date" => $this->created_at->format('F j, Y')
        ];
    }
}
