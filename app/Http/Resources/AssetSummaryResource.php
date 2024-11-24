<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Utilities;

class AssetSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "clientId" => $this->client_id,
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "totalActive" => $this->total_active,
            "totalInactive" => $this->total_inactive,
            "totalWorth" => $this->total_worth,
            "totalPurchaseWorth" => $this->total_purchase_worth,
            "appreciation" => Utilities::calculateAppreciation($this->total_worth, $this->total_purchase_worth)
        ];
    }
}
