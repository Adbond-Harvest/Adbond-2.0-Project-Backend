<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\AssessmentResource;
use app\Http\Resources\QuestionOptionResource;

class QuestionResource extends JsonResource
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
            "question" => $this->question,
            "assessment" => new AssessmentResource($this->whenLoaded('assessment')),
            "options" => QuestionOptionResource::collection($this->options)
        ];
    }
}
