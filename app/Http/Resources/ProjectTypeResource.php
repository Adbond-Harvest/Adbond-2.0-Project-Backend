<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\FileResource;
use App\Http\Resources\ProjectResource;

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
            "desccription" => $this->description,
            "photo" => new FileResource($this->photo),
            "projects" => ProjectResource::collection($this->whenLoaded("projects"))
        ];
    }
}
