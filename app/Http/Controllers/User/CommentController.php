<?php

namespace app\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Http\Controllers\Controller;

use app\Http\Requests\SaveComment;
use app\Http\Requests\React;

use app\Http\Resources\CommentResource;

use app\Models\Comment;
use app\Models\User;

use app\Services\CommentService;
use app\Services\ReactionService;

use app\Utilities;

class CommentController extends Controller
{
    private $commentService;
    private $reactionService;

    public function __construct()
    {
        $this->commentService = new CommentService;
        $this->reactionService = new ReactionService;
    }

    public function save(SaveComment $request)
    {
        try{
            $data = $request->validated();

            $data['commenterId'] = Auth::user()->id;
            $data['commenterType'] = User::$userType;

            $comment = $this->commentService->save($data);

            return Utilities::ok(new CommentResource($comment));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function delete($commentId)
    {
        if (!is_numeric($commentId) || !ctype_digit($commentId)) return Utilities::error402("Invalid parameter commentID");
        $comment = $this->commentService->comment($commentId);
        if(!$comment) return Utilities::error402("Comment not found");

        $this->commentService->delete($comment);

        return Utilities::okay("Comment Deleted");
    }

    public function react(React $request)
    {
        try{
            $data = $request->validated();
            if(!isset($data['commentId'])) return Utilities::error402("commentId is required");

            $data['entityType'] = Comment::$type;
            $data['entityId'] = $data['commentId'];
            $data['userType'] = User::$userType;
            $data['userId'] = Auth::user()->id;
            $data['reaction'] = ($data['reaction'] == 'like') ? 1 : 0;

            $reaction = $this->reactionService->userReaction($data['userId'], $data['userType'], $data['entityId'], $data['entityType']);

            $reaction = $this->reactionService->save($data, $reaction);

            return Utilities::okay("Successful");
            
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }
}
