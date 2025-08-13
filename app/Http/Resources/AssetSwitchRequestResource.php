<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\PackageResource;

class AssetSwitchRequestResource extends JsonResource
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
            "type" => $this->type,
            "packageFrom" => new PackageResource($this->packageFrom),
            "packageTo" => new PackageResource($this->packageTo),
            "status" => ($this->approved === null) ? "pending" : (($this->approved == 1) ? "Approved" : "Rejected"),
            "statusCheck" => ($this->approved === null) ? "pending" : $this->approved,
        ];
    }
}
