<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamesAccount extends Model
{
    protected $fillable = ['user_id', 'game', 'nickname', 'streak', 'profileId', 'accountId', 'verifyCode'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
