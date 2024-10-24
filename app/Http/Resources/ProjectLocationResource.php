<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\StateResource;
use app\Http\Resources\ProjectResource;

class ProjectLocationResource extends JsonResource
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
            "address" => $this->address,
            "state" => new StateResource($this->state),
            "project" => new ProjectResource($this->whenLoaded("project"))
        ];
    }
}
