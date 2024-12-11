<?php

namespace app\Services;

use app\Models\Comment;

class CommentService
{
    public $count = false;

    public function save($data)
    {
            $comment = new Comment;
            $comment->message = $data['message'];
            $comment->post_id = $data['postId'];
            $comment->commenter_type = $data['commenterType'];
            $comment->commenter_id = $data['commenterId'];

            $comment->save();
            
            return $comment;
    }

    public function delete($comment)
    {
        $comment->delete();
    }

    public function comments($with=[], $offset=0, $perPage=null)
    {
        $query = Comment::with($with);
        if($this->count) return $query->count();

        if($perPage==null) $perPage=env('PAGINATION_PER_PAGE');
        return $query->offset($offset)->limit($perPage)->orderBy("created_at", "DESC")->get();
    }

    public function comment($id, $with=[])
    {
        return Comment::with($with)->where("id", $id)->first();
    }
}