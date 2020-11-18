<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentPlayers extends Model
{
    protected $table = 'tournaments_players';

    protected $fillable = ['tournament_id', 'user_id', 'nickname' ,'roles', 'role', 'mmr', 'priority',  'team', 'profileId',  'accountId'];

    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class);
    }
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
