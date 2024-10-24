<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientNextOfKinResource extends JsonResource
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
            "title" => $this->title,
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "gender" => $this->gender,
            "phoneNumber" => $this->phone_number,
            "address" => $this->address,
            "relationship" => $this->relationship,
        ];
    }
}
