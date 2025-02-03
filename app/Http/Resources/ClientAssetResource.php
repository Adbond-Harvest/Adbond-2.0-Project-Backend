<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Enums\ClientPackageOrigin;

class ClientAssetResource extends JsonResource
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
            "project" => $this->package?->project?->name,
            "projectType" => $this->package?->project?->projectType?->name,
            "package" => $this->package?->name,
            "purchaseDate" => $this->created_at->format('F j, Y'),
            "amount" => $this->amount,
            "returns" => $this->interests(),
            "status" => ($this->purchase_complete==1) ? "completed" : "pending"
        ];
    }

    private function interests()
    {
        if($this->origin == ClientPackageOrigin::INVESTMENT->value) {
            if($this->purchase->interests) {
                $total = 0;
                if($this->purchase->interests->count() > 0) {
                    foreach($this->purchase->interests as $interest) {
                        $total += $interest->amount;
                    }
                }
                return $total;
            }
        }
        return null;
    }
}
