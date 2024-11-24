<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\PackageResource;

use app\Enums\ClientPackageOrigin;

use app\Utilities;

class AssetResource extends JsonResource
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
            "package" => $this->package?->name,
            "project" => $this->package?->project?->name,
            "projectType" => $this->package?->project?->projectType?->name,
            "purchaseAt" => $this->created_at->format('F j, Y'), 
            "amount" => ($this->origin == ClientPackageOrigin::ORDER->value) ? $this->purchase?->amount_payable : $this->purchase?->price,
            "appreciation" => $this->appreciation(),
            "active" => ($this->origin == ClientPackageOrigin::ORDER->value && !$this->purchase?->completed) ? true : false
        ];
    }

    private function appreciation()
    {
        $currentWorth = $this->package->amount * $this->purchase?->units ?? 0;
        // dd($currentWorth);
        $purchaseWorth = 0; 
        if($this->origin == ClientPackageOrigin::ORDER->value) {
            $purchaseWorth = ($this->purchase?->unit_price ?? 0) * ($this->purchase?->units ?? 0);
        }else{
            $purchaseWorth = $this->purchase?->price ?? 0;
        }
        // dd($purchaseWorth);
        return Utilities::calculateAppreciation($currentWorth, $purchaseWorth);
    }
}
