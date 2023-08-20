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
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Comment\StoreRequest  $request
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(comment $comment)
    {
        $comment->delete();
        
        return redirect()->back();
    }
}
