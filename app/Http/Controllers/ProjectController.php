<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Status;
use Illuminate\Http\Request;
use App\Http\Requests\Project\StoreRequest;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::all();

        return view('index')->with('projects', $projects);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::where('del_flg', 0)->orderBy('id')->get();
        return view('project.create')->with('users', $users);
    }

    /**
     * @param  \App\Http\Requests\Project\StoreRequest
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        $project = new Project();
        $project->__create($data);
        return redirect()->route('projects.index')->with('success', 'プロジェクトが保存されました');
    }

    /**
     * プロジェクト詳細画面
     * チケットの一覧を表示する
     * @param \Illuminate\Http\Request $request
     * @param int $projectId
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request, $projectId)
    {
        $project = Project::with('tickets.responsiblePerson')->find($projectId);

        $this->authorize('view', $project);
        
        $t_status = $request->input('t-status');
        $responsible = $request->input('responsible');

        $tickets = $project->tickets
            ->when(!empty($t_status) && $t_status !== "all" , function($q) use($t_status) {
                return $q->where('status_code', $t_status);
            })
            ->when(!empty($responsible) && $responsible !== "all" , function($q) use($responsible) {
                return $q->where('responsible_person_id', $responsible);
            });

        $statuses = Status::select('status_code', 'status_name')
            ->pluck('status_name', 'status_code');

        return view('project.detail')
            ->with('project', $project)
            ->with('tickets', $tickets)
            ->with('statuses', $statuses);
    }

    /**
     * @param  \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        $users = User::where('del_flg', 0)->orderBy('id')->get();
        
        return view('project.edit')
                ->with('project', $project)
                ->with('users', $users);
    }

    /**
     * @param  \App\Http\Requests\Project\StoreRequest
     * @param  \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function update(StoreRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $data = $request->validated();
        
        $project->__update($data);

        return redirect()->route('project.detail', $project);
    }

    /**
     * プロジェクトのステータスを更新
     *
     * @param  \Illuminate\Http\Request
     * @param  \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, Project $project)
    {
        $this->authorize('status', $project);

        if ($request->input('p-status') != '') {
            $project->status_code = $request->input('p-status') == 0 ? 'done' : 'active';
        }

        $project->save();

        if ($project->status_code == 'done') {
            return redirect()->route('projects.index');
        }
        return redirect()->route('project.detail', $project);
    }

    /**
     * @param  \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function delete(Project $project)
    {
        $this->authorize('delete', $project);

        $project->users()->detach();
        
        $project->delete();
        
        return redirect()->route('projects.index');
    }
}
