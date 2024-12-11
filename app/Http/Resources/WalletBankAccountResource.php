<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\BankResource;

class WalletBankAccountResource extends JsonResource
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
            "accountName" => $this->account_name,
            "accountNumber" => $this->account_number,
            "bank" => new BankResource($this->bank)
        ];
    }
}
