<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\IdentificationResource;
use app\Http\Resources\ClientBriefResource;
use app\Http\Resources\FileResource;

class ClientIdentificationResource extends JsonResource
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
            "client" => new ClientBriefResource($this->whenLoaded("client")),
            "identification" => new IdentificationResource($this->identification),
            "photo" => new FileResource($this->photo)
        ];
    }
}
