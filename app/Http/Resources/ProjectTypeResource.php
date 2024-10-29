<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\FileResource;
use app\Http\Resources\ProjectResource;

class ProjectTypeResource extends JsonResource
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
            "photo" => new FileResource($this->whenLoaded('photo')),
            "projects" => ProjectResource::collection($this->whenLoaded("projects"))
        ];
    }
}
