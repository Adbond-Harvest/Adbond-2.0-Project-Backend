<?php

namespace app\Http\Controllers\Client;

use Illuminate\Http\Request;
use app\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use app\Http\Requests\SaveComment;
use app\Http\Requests\React;

use app\Http\Resources\CommentResource;

use app\Models\Comment;
use app\Models\Client;

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

            $data['commenterId'] = Auth::guard("client")->user()->id;
            $data['commenterType'] = Client::$userType;

            $comment = $this->commentService->save($data);

            return Utilities::ok(new CommentResource($comment));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function react(React $request)
    {
        try{
            $data = $request->validated();
            if(!isset($data['commentId'])) return Utilities::error402("commentId is required");

            $data['entityType'] = Comment::$type;
            $data['entityId'] = $data['commentId'];
            $data['userType'] = Client::$userType;
            $data['userId'] = Auth::guard("client")->user()->id;
            $data['reaction'] = ($data['reaction'] == 'like') ? true : false;

            $reaction = $this->reactionService->userReaction($data['userId'], $data['userType'], $data['entityId'], $data['entityType']);

            $reaction = $this->reactionService->save($data, $reaction);

            return Utilities::okay("Successful");
            
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }
}
