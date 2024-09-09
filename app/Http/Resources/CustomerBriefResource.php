<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Helpers;

class CustomerBriefResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'photo' => new FileResource($this->photo),
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'country' => ($this->country) ? $this->country->name : null,
            'kycStatus' => $this->kyc_status,
            // 'kyc_completed' => Helpers::kycCompleted($this),
        ];
    }
}
