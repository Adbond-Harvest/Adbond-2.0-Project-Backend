<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\AssetResource;
use app\Http\Resources\WalletResource;
use app\Http\Resources\ClientNextOfKinResource;

use app\Enums\KYCStatus;

use app\Helpers;

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
            'othernames' => $this->othernames,
            'email' => $this->email,
            'photo' => new FileResource($this->photo),
            'phoneNumber' => $this->phone_number,
            'address' => $this->address,
            'gender' => $this->gender,
            'dob' => $this->dob,
            'country' => ($this->country) ? $this->country->name : null,
            'maritalStatus' => $this->marital_status,
            'employmentStatus' => $this->employment_status,
            'occupation' => $this->occupation,
            'kycStatus' => $this->verifyKYC($this),
            'refererCode' => $this->referer_code,
            'joinedAt' => $this->created_at->format('F j, Y'),
            // 'kyc_completed' => Helpers::kycCompleted($this),
            'active' => ($this->activated && $this->activated == 1) ? true : false,
            'wallet' => new WalletResource($this->wallet),
            'assets' => AssetResource::collection($this->whenLoaded('assets')),
            'nextOfKin' => new ClientNextOfKinResource($this->whenLoaded("nextOfKins"))
            // 'passwordSet' => ($this->password_set)
        ];
    }

    private function verifyKYC($client)
    {
        $status = KYCStatus::NOTSTARTED->value;
        $started = Helpers::kycStarted($client);
        if($started) {
            $completed = Helpers::kycCompleted($client);
            $status = ($completed) ? KYCStatus::COMPLETED->value : KYCStatus::STARTED->value;
        }else{
            $status = KYCStatus::NOTSTARTED->value;
        }
        if($client->kyc_status != $status) {
            Helpers::updateKYC($client->id, $status);
        }
        return $status;
    }
}
