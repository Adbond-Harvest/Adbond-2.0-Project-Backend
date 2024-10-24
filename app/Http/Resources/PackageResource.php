<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\ProjectResource;
use app\Http\Resources\StateResource;
use app\Http\Resources\FileResource;

class PackageResource extends JsonResource
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
            "name" => $this->name,
            "project" => new ProjectResource($this->whenLoaded("project")),
            "size" => $this->size,
            "amount" => $this->amount,
            "units" => $this->units,
            "availableUnits" => $this->available_units,
            "discounts" => $this->discounts,
            "minPrice" => $this->min_price,
            "installmentDuration" => $this->installment_duration,
            "infrastructureFee" => $this->infrastructure_fee,
            "description" => $this->description,
            "benefits" => $this->benefits,
            "installmentOption" => $this->installment_option,
            "vrUrl" => $this->vr_url,
            "active" => ($this->active) ? true : false,
            "status" => ($this->units==0 || $this->sold_out) ? "Sold Out" : (($this->active) ? "Active" : "Inactive"),
            "soldOut" => ($this->units==0 || $this->sold_out) ? true : false,
            "state" => new StateResource($this->whenLoaded("state")),
            "address" => $this->address,
            "location" => $this->address." ".$this->state?->name,
            "brochure" => new FileResource($this->whenLoaded("brochure")),
            "photos" => FileResource::collection($this->whenLoaded("photos"))
        ];
    }
}
