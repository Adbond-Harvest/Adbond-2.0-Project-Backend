<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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
        $resource = [
            "id" => $this->id,
            "slug" => $this->slug,
            "topic" => $this->topic,
            "type" => $this->post_type,
            "coverPhoto" => new FileResource($this->coverPhoto),
            "content" => $this->content,
            "active" => ($this->active == 1) ? true : false,
            "created" => $this->created_at->format('F j, Y'),
            "liked" => $this->liked(),
            "disliked" => $this->disliked(),
            "comments" => CommentResource::collection($this->whenLoaded('comments'))
        ];

        $resource["commentsCount"] = $this->comments->count();
        $resource["likesCount"] = $this->likes->count();
        $resource["dislikesCount"] = $this->dislikes->count();

        return $resource;
    }

    private function liked()
    {
        $liked = false;
        $user = Auth::user() ?? Auth::guard("client")->user();
        if($user) {
            $likedPostIds = $user->likedPosts()->pluck("posts.id")->toArray();
            $liked = (in_array($this->id, $likedPostIds));
        }
        return $liked;
    }

    private function disliked()
    {
        $disliked = false;
        $user = Auth::user() ?? Auth::guard("client")->user();
        if($user) {
            $likedPostIds = $user->dislikedPosts()->pluck("posts.id")->toArray();
            $disliked = (in_array($this->id, $likedPostIds));
        }
        return $disliked;
    }
}
