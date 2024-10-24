<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\ProjectTypeResource;
use app\Http\Resources\PackageResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = [
            "id" => $this->id,
            "identifier" => $this->identifier,
            "name" => $this->name,
            "description" => $this->description,
            "status" => ($this->active) ? "Active" : "Inactive",
            "created" => $this->created_at->format("F j, Y"),
            "projectType" => new ProjectTypeResource($this->whenLoaded("projectType")),
            "packages" => PackageResource::collection($this->whenLoaded("packages"))
            // "locations" => ProjectLocationResource::collection($this->whenLoaded("locations"))
        ];
        $resource['packageCount'] = $this->packages->count();
        $resource['canDelete'] = $this->canDelete();

        return $resource;
    }
}
