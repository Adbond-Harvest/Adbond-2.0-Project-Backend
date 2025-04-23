<?php

namespace app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CommentResource extends JsonResource
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
            "message" => $this->message,
            "commenter" => $this->commenter->name,
            "liked" => $this->liked(),
            "disliked" => $this->disliked(),
            "addedAt" => $this->created_at->format('F j, Y'),
            "likesCount" => $this->likes->count(),
            "dislikesCount" => $this->dislikes->count()
        ];
    }

    private function liked()
    {
        $liked = false;
        $user = Auth::user() ?? Auth::guard("client")->user();
        if($user) {
            $likedCommentIds = $user->likedComments()->pluck("comments.id")->toArray();
            $liked = (in_array($this->id, $likedCommentIds));
        }
        return $liked;
    }

    private function disliked()
    {
        $disliked = false;
        $user = Auth::user() ?? Auth::guard("client")->user();
        if($user) {
            $likedCommentIds = $user->dislikedComments()->pluck("comments.id")->toArray();
            $disliked = (in_array($this->id, $likedCommentIds));
        }
        return $disliked;
    }
}
