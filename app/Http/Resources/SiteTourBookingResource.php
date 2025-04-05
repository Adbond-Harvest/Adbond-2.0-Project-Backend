<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\SiteTourScheduleResource;
use app\Http\Resources\ClientBriefResource;

class SiteTourBookingResource extends JsonResource
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
            "schedule" => new SiteTourScheduleResource($this->whenLoaded("schedule")),
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "email" => $this->email,
            "phoneNumber" => $this->phone_number
            // "client" => new ClientBriefResource($this->whenLoaded("client"))
        ];
    }
}
