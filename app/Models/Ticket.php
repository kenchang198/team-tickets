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
}
