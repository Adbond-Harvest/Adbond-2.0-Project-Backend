<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'title' => $this->title,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'photo' => new FileResource($this->photo),
            'phoneNumber' => $this->phone_number,
            'address' => $this->address,
            'gender' => $this->gender,
            'dob' => $this->dob,
            'country' => ($this->country) ? $this->country->name : null,
            'maritalStatus' => $this->marital_status,
            'occupation' => $this->occupation,
            'kycStatus' => $this->kyc_status,
            // 'passwordSet' => ($this->password_set)
            // 'kyc_completed' => Helpers::kycCompleted($this),
        ];
    }
}
