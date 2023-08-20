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

    public function CreatedAt()
    {
        return \Carbon\Carbon::parse($this->created_at)->format('Y/m/d H:i:s');
    }

    public function user()
    {
        // comments.user_id = users.id
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function __create(array $data, String $ticket_id)
    {
        $this->create(
            [
                'ticket_id' => $ticket_id,
                'user_id' => Auth::user()->id,
                'comment' => $data['comment'],
            ]
        );
    }

    public function __update(String $comment)
    {
        $this->comment = $comment;
        $this->save();
    }
}
