<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

class ClientInvestmentResource extends JsonResource
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
            "package" => $this->package?->name,
            "redemptionOption" => $this->redemption_option,
            "capital" => $this->capital,
            "intervalDuration" => $this->duration,
            "timeline" => $this->timeline,
            "started" => ($this->started == 1) ? true : false,
            "startedAt" => Carbon::parse($this->start_date)->format('F j, Y'),  
            "active" => ($this->started == 1 && $this->ended == 0) ? true : false,
            "files" => FileResource::collection($this->files)
        ];
    }
}
