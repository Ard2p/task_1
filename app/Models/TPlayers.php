<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TPlayers extends Model
{
    protected $table = 'tournaments_players';

    protected $fillable = ['tournament_id', 'user_id', 'account_id', 'role', 'team'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gameAccount()
    {
        return $this->hasOne(GamesAccount::class);
    }
}
