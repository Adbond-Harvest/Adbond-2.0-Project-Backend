<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\ProjectTypeResource;
use app\Http\Resources\ProjectResource;
use app\Http\Resources\PackageResource;
use app\Http\Resources\SiteTourBookingResource;

use Carbon\Carbon;

class SiteTourBookedScheduleResource extends JsonResource
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
            "projectType" => new ProjectTypeResource($this->schedule->projectType),
            "project" => new ProjectResource($this->schedule->project),
            "package" => new PackageResource($this->schedule->package),
            "bookedDate" => $this->booked_date,
            "bookedTime" => Carbon::createFromFormat('H:i:s', $this->schedule->available_time)->format('h:i A'),
            "visited" => ($this->visited == 1) ? true : false,
            "cancelled" => ($this->cancelled == 1) ? true : false,
            "bookings" => SiteTourBookingResource::collection($this->whenLoaded("bookings"))
        ];
    }
}
