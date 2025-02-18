<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientReferralEarningResource extends JsonResource
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
            "client" => $this->client->name,
            "package" => $this->order->package->name,
            "units" => $this->order->units,
            "amount" => $this->order->amount_payed,
            "commission" => $this->amount_after_tax,
            "date" => $this->created_at->format('F j, Y')
        ];
    }
}
