<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Enums\StaffCommissionType;

class StaffCommissionEarningResource extends JsonResource
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
            "name" => $this->getName(),
            "userType" => ($this->type == StaffCommissionType::DIRECT->value) ? "Client" : "Staff",
            "type" => $this->type,
            "amount" => $this->commission_after_tax
        ];
    }

    private function getName()
    {
        if($this->type == StaffCommissionType::DIRECT->value) return $this->order?->client?->name;
        return $this->order?->client?->referer?->name;
    }
}
