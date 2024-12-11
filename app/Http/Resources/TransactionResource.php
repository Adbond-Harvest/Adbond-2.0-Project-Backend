<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            "refId" => $this->receipt_no,
            "package" => $this->purchase?->package?->name,
            "project" => $this->purchase?->package?->project?->name,
            "projectType" => $this->purchase?->package?->project?->projectType?->name,
            "amount" => $this->amount,
            "status" => ($this->confirmed == 1) ? "Successful" : (($this->success === 0) ? "Failed" : "Pending"),
            "paymentMode" => $this->paymentMode?->name,
            "date" => $this->created_at->format('F j, Y')
        ];
    }
}
