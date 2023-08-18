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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::all();

        return view('index')->with('projects', $projects);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::where('del_flg', 0)->orderBy('id')->get();
        return view('project.create')->with('users', $users);
    }

    /**
     * Store a newly created resource in storage.
     *
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
     * @param String $id
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request, $id)
    {
        $project = Project::find($id);
        
        $this->authorize('view', $project);
        
        $t_status = $request->input('t-status');
        
        $responsible = $request->input('responsible');

        $tickets = $project->tickets()
            ->when(!empty($t_status) && $t_status !== "all" , function($q) use($t_status) {
                return $q->where('status_code', $t_status);
            })
            ->when(!empty($responsible) && $responsible !== "all" , function($q) use($responsible) {
                return $q->where('responsible_person_id', $responsible);
            })
            ->get();

        $statuses = Status::select('status_code', 'status_name')
            ->pluck('status_name', 'status_code');

        return view('project.detail')
            ->with('project', $project)
            ->with('tickets', $tickets)
            ->with('statuses', $statuses);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $project = Project::find($id);
        
        $this->authorize('update', $project);

        $users = User::where('del_flg', 0)->orderBy('id')->get();
        
        return view('project.edit')
                ->with('project', $project)
                ->with('users', $users);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Project\StoreRequest
     * @return \Illuminate\Http\Response
     */
    public function update(StoreRequest $request)
    {
        $data = $request->validated();
        
        $project = Project::where('id', $request->id())->first();
        
        $this->authorize('update', $project);
        
        $project->__update($data);

        return redirect()->route('project.detail', ['id' => $project->id]);
    }

    /**
     * プロジェクトのステータスを更新
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id)
    {
        $project = Project::where('id', $id)->first();
        
        $this->authorize('status', $project);

        if ($request->input('p-status') != '') {
            $project->status_code = $request->input('p-status') == 0 ? 'done' : 'active';
        }

        $project->save();

        if ($project->status_code == 'done') {
            return redirect()->route('projects.index');
        }
        return redirect()->route('project.detail', ['id' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\Project\StoreRequest
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $project = Project::find($id);
        
        $this->authorize('delete', $project);

        $project->users()->detach();
        
        $project->delete();
        
        return redirect()->route('projects.index');
    }
}
