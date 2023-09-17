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
    /**
     * @param \App\Http\Requests\Comment\StoreRequest $request
     * @param \App\Models\Project $project
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request, Project $project, Ticket $ticket)
    {
        $data = $request->validated();
        
        $comment = Comment::create(
            [
                'ticket_id' => $ticket->id,
                'user_id' => Auth::user()->id,
                'comment' => $data['comment'],
            ]
        );
        
        return response()->json(
            [
                'success' => true,
                'username' => Auth::user()->name,
                'created_at' => $comment->createdAt(),
                'id' => $comment->id,
                'update_url' => route('comment.update', $comment),
                'delete_url' => route('comment.delete', $comment),
            ]
        );
    }
    
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        $id = $comment->id;
        
        $data = $request->validate(
            [
                'comment-' . $id => 'required|string|max:1000',
            ],
            [
                'comment-' . $id . '.required' => 'コメントを入力してください。',
                'comment-' . $id . '.max' => 'コメントは、:max文字以下で入力してください。',
            ]
        );

        $comment->__update($data["comment-$id"]);
        
        $redirectUrl = URL::previous() . "#comment-$id";
        
        return redirect($redirectUrl);
    }

    /**
     * @param \App\Models\comment $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(comment $comment)
    {
        $comment->delete();
        
        return redirect()->back();
    }
}
