<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;

class Ticket extends Model
{
    use HasFactory;
    protected $table = 'tickets';
    public $timestamps = false;

    protected $fillable = [
        'ticket_name',
        'responsible_person_id',
        'project_id',
        'content',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'created_user_id',
        'updated_user_id'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    
    public function responsiblePerson()
    {
        return $this->belongsTo(User::class, 'responsible_person_id');
    }

    public function createUser()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function updateUser()
    {
        return $this->belongsTo(User::class, 'updated_user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getUserIdsArray()
    {
        // 特定のTicketに関連するUserモデルを取得
        $users = $this->users;

        // Userモデルの情報を連想配列に変換
        $userData = $users->map(function ($user) {
            return $user->id;
        });

        return $userData->toArray();
    }

    public function hasUpdatePolicy()
    {
        return Auth::user()->admin 
            || $this->responsible_person_id === Auth::user()->id
            || $this->created_user_id === Auth::user()->id;
    }

    public function hasDeletePolicy()
    {
        $project = Project::select('responsible_person_id')
        ->where('id', $this->project_id)->first();
        
        return Auth::user()->admin 
            || $project->responsible_person_id === Auth::user()->id
            || $this->responsible_person_id === Auth::user()->id
            || $this->created_user_id === Auth::user()->id;
    }

    public function __create(array $data, String $project_id)
    {
        $ticket = $this->create(
            [
                'ticket_name' => $data['ticket_name'],
                'responsible_person_id' => $data['t_responsible_person_id'],
                'project_id' => $project_id,
                'content' => $data['content'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_user_id' => Auth::user()->id,
                'updated_user_id' => Auth::user()->id
            ]
        );

        if (isset($data['user_id'])) {
            $ticket->users()->attach($data['user_id']);
        }
    }
}
