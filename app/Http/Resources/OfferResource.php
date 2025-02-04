<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\ClientAssetResource;
use app\Http\Resources\ClientBriefResource;

class OfferResource extends JsonResource
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
            "asset" => new ClientAssetResource($this->asset),
            "units" => $this->units,
            "price" => $this->price,
            "active" => ($this->active==1) ? true : false,
            "approved" => ($this->approved==1) ? true : false,
            "completed" => ($this->completed==1) ? true : false,
            "rejectedReason" => $this->rejected_reason,
            "paymentStatus" => $this?->paymentStatus?->name,
            "treatedBy" => $this->user?->name,
            "date" => $this->created_at->format('F j, Y'),
            "client" => new ClientBriefResource($this->whenLoaded("client"))
        ];
    }
}
