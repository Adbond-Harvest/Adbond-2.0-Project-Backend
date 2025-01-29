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
            "projectType" => new ProjectTypeResource("projectType"),
            "project" => new ProjectResource("project"),
            "package" => new PackageResource("package"),
            "availableDate" => $this->available_date,
            "availableTime" => Carbon::createFromFormat('H:i', $this->available_time)->format('h:i A')
        ];
    }
}
