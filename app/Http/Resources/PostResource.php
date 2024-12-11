<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use app\Http\Resources\FileResource;
use app\Http\Resources\CommentResource;

class PostResource extends JsonResource
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
            "topic" => $this->topic,
            "type" => $this->post_type,
            "file" => new FileResource($this->file),
            "content" => $this->content,
            "active" => ($this->active == 1) ? true : false,
            "created" => $this->created_at->format('F j, Y'),
            "comments" => CommentResource::collection($this->comments)
        ];
    }
}
