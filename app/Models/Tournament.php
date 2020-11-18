<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Collective\Html\Eloquent\FormAccessible;

class Tournament extends Model
{
    use FormAccessible;

    protected $fillable = ['user_id', 'provider_id', 'name', 'img' ,'desc', 'twitch', 'game', 'type', 'wins',  'teams', 'status', 'history'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function players()
    {
        return $this->hasMany(TournamentPlayers::class);
    }
}
