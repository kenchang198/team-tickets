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
        
        $comment = new Comment;
        $comment->__create($data, $ticket->id);

        return redirect()->route('ticket.show', [$project, $ticket]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        $comment->__update($request->input('comment'));

        $id = $comment->id;
        
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
