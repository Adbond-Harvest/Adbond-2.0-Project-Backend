<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "total" => $this->total_clients,
            "active" => $this->active_clients,
            "inactive" => $this->inactive_clients,
            "purchasingClients" => $this->purchasing_clients,
            "newClients" => $this->new_clients
        ];
    }
}
