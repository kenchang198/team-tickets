<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Http\Requests\Comment\StoreRequest;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Ticket;

class CommentController extends Controller
{
    
    // コメント一覧を取得するAPI
    public function index(Ticket $ticket)
    {
        $comments = $ticket->comments->load('user');
        
        return response()->json(
            [
                'success' => true,
                'comments' => $comments,
                'csrf_token' => csrf_token(),
            ]
        );
    }

    /**
     * @param \App\Http\Requests\Comment\StoreRequest $request
     * @param \App\Models\Project $project
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request, Project $project, Ticket $ticket)
    {
        $data = $request->validated();
        
        Comment::create(
            [
                'ticket_id' => $ticket->id,
                'user_id' => Auth::user()->id,
                'comment' => $data['comment'],
            ]
        );
        
        return response()->json(
            [
                'success' => true,
            ]
        );
    }
    
    /**
     * @param \App\Http\Requests\Comment\StoreRequest $request
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function update(StoreRequest $request, Comment $comment)
    {
        $data = $request->validated();
        
        $comment->__update($data["comment"]);
        
        return response()->json(
            [
                'success' => true,
            ]
        );
    }

    /**
     * @param \App\Models\comment $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(comment $comment)
    {
        $comment->delete();
        
        return response()->json(
            [
                'success' => true
            ]
        );
    }
}
