<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use app\Http\Resources\ProjectTypeResource;
use app\Http\Resources\ProjectResource;
use app\Http\Resources\PackageResource;

use Carbon\Carbon;

class SiteTourScheduleResource extends JsonResource
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
            "projectType" => new ProjectTypeResource($this->projectType),
            "project" => new ProjectResource($this->project),
            "package" => new PackageResource($this->package),
            "fee" => $this->fee,
            "availableDate" => $this->available_date,
            "availableTime" => Carbon::createFromFormat('H:i:s', $this->available_time)->format('h:i A'),
            "visited" => ($this->visited == 1) ? true : false,
            "cancelled" => ($this->cancelled == 1) ? true : false
        ];
    }
}
