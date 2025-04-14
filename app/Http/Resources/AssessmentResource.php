<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\QuestionResource;

class AssessmentResource extends JsonResource
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
            "title" => $this->title,
            "description" => $this->description,
            "active" => ($this->active == 1) ? true : false,
            "questions" => QuestionResource::collection($this->questions)
        ];
    }
}
