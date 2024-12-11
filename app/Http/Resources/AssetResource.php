<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\PackageResource;
use app\Http\Resources\FileResource;

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
            "project_identifier" => $this->identifier,
            "project" => $this->package?->project?->name,
            "projectType" => $this->package?->project?->projectType?->name,
            "purchaseAt" => $this->created_at->format('F j, Y'), 
            "amount" => ($this->origin == ClientPackageOrigin::ORDER->value) ? $this->purchase?->amount_payable : $this->purchase?->price,
            "paymentPlan" => ($this->origin == ClientPackageOrigin::ORDER->value && $this->purchase->installment == 1) ? "installment" : "one-off",
            "appreciation" => $this->appreciation(),
            "status" => ($this->origin == ClientPackageOrigin::ORDER->value && $this->purchase->completed == 0) ? "pending" : "completed", 
            "active" => ($this->origin == ClientPackageOrigin::ORDER->value && !$this->purchase?->completed) ? true : false,
            "files" => FileResource::collection($this->files)
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
