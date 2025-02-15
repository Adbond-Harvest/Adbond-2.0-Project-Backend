<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\OfferResource;
use app\Http\Resources\ClientBriefResource;

class OfferBidResource extends JsonResource
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
            "price" => $this->price,
            "status" =>  ($this->accepted == null) ? (($this->accepted == 1) ? "Accepted" : "Rejected") : "Pending",
            "date" => $this->created_at->format('F j, Y'),
            "offer" => new OfferResource($this->whenLoaded('offer')),
            "client" => new ClientBriefResource($this->whenLoaded("client"))
        ];
    }
}
