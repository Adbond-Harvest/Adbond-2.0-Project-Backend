<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\AssessmentResource;
use app\Http\Resources\AssessmentAttemptAnswerResource;

class AssessmentAttemptResource extends JsonResource
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
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "email" => $this->email,
            "phoneNumber" => $this->phone_number,
            "gender" => $this->gender,
            "address" => $this->address,
            "occupation" => $this->occupation,
            "score" => $this->score,
            "passed" => ($this->passed === null) ? "pending" : (($this->passed == 1) ? true : false),
            "answers" => AssessmentAttemptAnswerResource::collection($this->answers),
            "assessment" => new AssessmentResource($this->assessment)
        ];
    }
}
