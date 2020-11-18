<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatisticPlayers extends Model
{ 
    protected $table = 'players_statistics';

    protected $fillable = ['user_id', 'game', 'win', 'lose', 'k', 'd', 'a'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->hasOne(GamesAccount::class, 'user_id', 'user_id');
    }
}
