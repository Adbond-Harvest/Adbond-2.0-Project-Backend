<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\ClientAssetResource;
use app\Http\Resources\ClientBriefResource;

use app\Utilities;

class AssetDowngradeResource extends JsonResource
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
            "client" => new ClientBriefResource($this->whenLoaded("client")),
            "packageFrom" => new PackageResource($this->packageFrom),
            "packageTo" => new PackageResource($this->packageTo),
            "asset" => new ClientAssetResource($this->asset),
            "penalty" => $this->penalty."%",
            "penaltyAmount" => $this->penalty_amount
        ];
    }
}
