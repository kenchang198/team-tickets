<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Project extends Model
{
    use HasFactory;
    protected $table = 'projects';
    public $timestamps = false; // タイムスタンプのカラムが存在しないため、falseに設定します

    protected $fillable = [
        'project_name',
        'responsible_person_id',
        'start_date',
        'end_date',
        'status_code',
        'created_user_id',
        'updated_user_id'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user');
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

    public function hasPolicy()
    {
        return Auth::user()->admin 
            || $this->responsiblePerson->id === Auth::user()->id
            || $this->created_user_id === Auth::user()->id;
    }

    public function __create(array $data)
    {
        $project = $this->create([
            'project_name' => $data['project_name'],
            'responsible_person_id' => $data['responsible_person_id'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_user_id' => Auth::user()->id,
            'updated_user_id' => Auth::user()->id,
        ]);

        // メンバーの関連付け
        if (isset($data['user_id'])) {
            $project->users()->attach($data['user_id']);
        }

        // プロジェクトの責任者がプロジェクトメンバーに含まれていない場合は追加する
        if (!in_array($data['responsible_person_id'], $data['user_id'], true)) {
            $project->users()->attach($data['responsible_person_id']);
        }
    }

    public function __update(array $data)
    {
        $this->project_name = $data['project_name'];
        $this->responsible_person_id = $data['responsible_person_id'];

        $this->updated_at = date('Y-m-d H:i:s');
        $this->updated_user_id = Auth::user()->id;

        $this->save();

        // プロジェクトメンバーを初期化
        $this->users()->detach();

        // メンバーの関連付け
        if (isset($data['user_id'])) {
            $this->users()->attach($data['user_id']);
        }

        // プロジェクトの責任者がプロジェクトメンバーに含まれていない場合は追加する
        if (!in_array($data['responsible_person_id'], $data['user_id'], true)) {
            $this->users()->attach($data['responsible_person_id']);
        }
    }
}
