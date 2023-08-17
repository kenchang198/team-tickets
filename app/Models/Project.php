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
        'status_code'
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
}
