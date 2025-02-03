<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientAssetSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "total" => $this?->total_packages ? $this->total_packages : 0,
            "pending" => $this?->total_active ? $this->total_active : 0,
            "completed" => $this?->total_inactive ? $this->total_inactive : 0
        ];
    }
}
