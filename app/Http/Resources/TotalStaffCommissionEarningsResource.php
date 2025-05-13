<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\UserBriefResource;

use app\Models\User;
use app\Services\CommentService;
use app\Services\CommissionService;

class TotalStaffCommissionEarningsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "totalEarnings" => $this->total_earnings,
            "totalRedemptions" => $this->totalRedemptions(),
            "balance" => $this->total_earnings - $this->totalRedemptions(),
            "user" => new UserBriefResource($this->user())
        ];
    }

    private function user()
    {
        return User::find($this->user_id);
    }

    private function totalRedemptions()
    {
        $commissionService = new CommissionService;
        return $commissionService->getTotalStaffRedemptions($this->user_id);
    }

}
