<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\BankResource;

class BankAccountResource extends JsonResource
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
            "bank" => new BankResource($this->bank),
            "accountNumber" => $this->number,
            "name" => $this->name,
            "active" => ($this->active==1) ? true : false
        ];
    }
}
