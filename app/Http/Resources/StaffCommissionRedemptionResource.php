<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\UserBriefResource;
use app\Http\Resources\UserBankAccountResource;

class StaffCommissionRedemptionResource extends JsonResource
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
            "amount" => $this->amount,
            "statius" => $this->status,
            "user" => new UserBriefResource($this->whenLoaded("user")),
            "bankAccount" => new UserBankAccountResource($this->bankAccount)
        ];
    }
}
