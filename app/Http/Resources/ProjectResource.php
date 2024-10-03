<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\ProjectTypeResource;
use App\Http\Resources\ProjectLocationResource;

class ProjectResource extends JsonResource
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
            "name" => $this->name,
            "description" => $this->description,
            "status" => ($this->active) ? "Active" : "Inactive",
            "created" => $this->created_at->format("F j, Y"),
            "projectType" => new ProjectTypeResource($this->whenLoaded("projectType")),
            "locations" => ProjectLocationResource::collection($this->whenLoaded("locations"))
        ];
    }
}
