<?php

namespace app\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Http\Controllers\Controller;

use app\Http\Requests\SaveComment;

use app\Http\Resources\CommentResource;

use app\Models\Comment;
use app\Models\User;

use app\Services\CommentService;

use app\Utilities;

class CommentController extends Controller
{
    private $commentService;

    public function __construct()
    {
        $this->commentService = new CommentService;
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
}
