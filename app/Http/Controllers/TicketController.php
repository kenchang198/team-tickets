<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Status;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Requests\Ticket\StoreRequest;
use App\Http\Requests\Ticket\UpdateRequest;
use App\Http\Requests\Ticket\StatusRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function create(Project $project)
    {
        $old_user_id = [];
        
        $session_data = session()->all();

        // 選択中の要確認メンバー(id)を取得
        if (isset($session_data["_old_input"])) {
            
            if (isset($session_data["_old_input"]["user_id"])) {
                $old_user_id = $session_data["_old_input"]["user_id"];
            }
        }
        
        $this->authorize('view', $project);
        
        return view('ticket.create')
            ->with('project', $project)
            ->with('old_user_id', $old_user_id);
    }

    /**
     * @param \App\Http\Requests\Ticket\StoreRequest
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request, Project $project)
    {
        $this->authorize('view', $project);

        $data = $request->validated();

        $ticket = new Ticket();

        $ticket->__create($data, $project->id);

        return redirect()->route('project.detail', $project);
    }

    /**
     * @param \App\Models\Project $project
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project, Ticket $ticket)
    {
        $this->authorize('view', $project);

        $statuses = Status::select('status_code', 'status_name')
            ->pluck('status_name', 'status_code');

        return view('ticket.detail')
            ->with('project', $project)
            ->with('ticket', $ticket)
            ->with('start_date_f', \Carbon\Carbon::parse($ticket->start_date)->format('Y/m/d'))
            ->with('end_date_f', \Carbon\Carbon::parse($ticket->end_date)->format('Y/m/d'))
            ->with('created_at', \Carbon\Carbon::parse($ticket->created_at)->format('Y/m/d'))
            ->with('updated_at', \Carbon\Carbon::parse($ticket->updated_at)->format('Y/m/d'))
            ->with('statuses', $statuses);
    }

    /**
     * @param \App\Models\Project $project
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project, Ticket $ticket)
    {
        $old_user_id = [];
        
        $session_data = session()->all();
        
        // 選択中の要確認メンバー(id)を取得
        if (isset($session_data["_old_input"])) {
            if (isset($session_data["_old_input"]["user_id"])) {
                $old_user_id = $session_data["_old_input"]["user_id"];
            }
        }

        $this->authorize('update', $ticket);
        
        return view('ticket.edit')
            ->with('project', $project)
            ->with('ticket', $ticket)
            ->with('start_date_f', \Carbon\Carbon::parse($ticket->start_date)->format('Y-m-d'))
            ->with('end_date_f', \Carbon\Carbon::parse($ticket->end_date)->format('Y-m-d'))
            ->with('old_user_id', $old_user_id);
    }

    /**
     * @param \App\Http\Requests\Ticket\UpdateRequest
     * @param \App\Models\Project $project
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Project $project, Ticket $ticket)
    {
        $this->authorize('view', $project);

        $data = $request->validated();

        $this->authorize('update', $ticket);

        $ticket->__update($data);
        
        return redirect()->route('ticket.show', [$project, $ticket]);
    }

    /**
     * @param \App\Http\Requests\Ticket\StatusRequest
     * @param \App\Models\Project $project
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\Response
     */
    public function status(StatusRequest $request, Project $project, Ticket $ticket)
    {
        $this->authorize('view', $project);

        $data = $request->validated();

        $ticket->status_code = $data['t-status'];

        $ticket->save();

        if ($ticket->status_code === 'done') {
            return redirect()->route('project.detail', $project);
        }

        return redirect()->route('ticket.show', [$project, $ticket]);
    }

    /**
     * @param \App\Models\Project $project
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\Response
     */
    public function delete(Project $project, Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        $ticket->users()->detach();

        $ticket->delete();
        
        return redirect()->route('project.detail', $project);
    }
}
