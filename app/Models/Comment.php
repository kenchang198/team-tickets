<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    public function CreatedAt()
    {
        return \Carbon\Carbon::parse($this->created_at)->format('Y/m/d H:i:s');
    }

    public function user()
    {
        // comments.user_id = users.id
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
