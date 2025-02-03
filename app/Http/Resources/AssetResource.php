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
            "amount" => $this->amount, //($this->origin == ClientPackageOrigin::ORDER->value) ? $this->purchase?->amount_payable : $this->purchase?->price,
            "location" => $this->package?->state,
            "address" => $this->package?->address,
            "description" => $this->package?->description,
            "units" => $this->units,
            "size" => $this->package?->size,
            "amountPaid" => $this->amountPaid(),
            "paymentPlan" => $this->paymentPlan(),
            "installmentCount" => $this->installmentCount(),
            "nextPaymentDate" => $this->payment_due_date,
            "appreciation" => $this->appreciation(),
            "balance" => $this->balance(),
            "status" => ($this->origin == ClientPackageOrigin::ORDER->value && $this->purchase?->completed == 0) ? "pending" : "completed", 
            "active" => ($this->origin == ClientPackageOrigin::ORDER->value && !$this->purchase?->completed) ? true : false,
            "files" => FileResource::collection($this->files),
            "returns" => $this->investmentReturns()
        ];
    }

    // private function package()
    // {
    //     if($this->origin == ClientPackageOrigin::INVESTMENT->value) {
    //         $package = $this->purchase->order
    //     }
    // }

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

    private function amountPaid()
    {
        if($this->purchase_complete==0) {
            if($this->origin == ClientPackageOrigin::INVESTMENT->value) return $this->purchase->order->amount_payed;
            return $this->purchase?->amount_payed;
        }
        return $this->amount;
    }

    private function balance()
    {
        if($this->origin == ClientPackageOrigin::ORDER->value) {
            return $this->purchase?->balance;
        }
        if($this->origin == ClientPackageOrigin::INVESTMENT->value) {
            return $this->purchase?->order?->balance;
        }
        return 0;
    }

    private function installmentCount()
    {
        if($this->origin == ClientPackageOrigin::ORDER->value || $this->origin == ClientPackageOrigin::INVESTMENT->value) {
            $order = ($this->origin == ClientPackageOrigin::ORDER->value) ? $this?->purchase : $this->purchase->order;
            return ($order?->is_installment == 1) ? $order->installment_count : null;
        }
        return null;
    }

    private function paymentPlan()
    {
        
        if($this->origin == ClientPackageOrigin::ORDER->value || $this->origin == ClientPackageOrigin::INVESTMENT->value) {
            $order = ($this->origin == ClientPackageOrigin::ORDER->value) ? $this?->purchase : $this->purchase->order;
            return ($order?->is_installment == 1) ? "installment" : "one-off";
        }
        return "one-off";
    }

    private function investmentReturns()
    {
        if($this->origin == ClientPackageOrigin::INVESTMENT->value) {
            $investment = $this->purchase;
            if($investment) {
                return [
                    "returns" => ($investment->amount) ? $investment->amount : $investment->percentage.'%',
                    "timeline" => $investment->duration.'Months',
                    "duration" => $investment->timeline.'Months'
                ];
            }
        }
        return null;
    }
}
