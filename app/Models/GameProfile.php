<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameProfile extends Model
{
    protected $fillable = ['user_id', 'game', 'mmr', 'streak', 'priority', 'roles'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
