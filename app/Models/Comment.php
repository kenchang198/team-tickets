<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'ticket_id',
        'user_id',
        'comment'
    ];

    public function createdAt()
    {
        return \Carbon\Carbon::parse($this->created_at)->format('Y/m/d H:i');
    }

    public function user()
    {
        // comments.user_id = users.id
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function __update(String $comment)
    {
        $this->comment = $comment;
        $this->save();
    }
}
