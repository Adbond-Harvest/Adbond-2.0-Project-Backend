<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\QuestionResource;
use app\Http\Resources\AssessmentAttemptResource;

class AssessmentResource extends JsonResource
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
            "title" => $this->title,
            "description" => $this->description,
            "instructions" => $this->instructions,
            "duration" => $this->duration,
            "cutOffMark" => $this->cut_off_mark,
            "active" => ($this->active == 1) ? true : false,
            "questions" => QuestionResource::collection($this->questions),
            "attempts" => AssessmentAttemptResource::collection($this->whenLoaded("attempts"))
        ];

        $resource["submissions"] = $this->attempts->count();

        return $resource;
    }
}
