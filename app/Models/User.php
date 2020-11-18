<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\GamesAccount;

class User extends Authenticatable
{
    // use Notifiable;
   
    // protected $with = ['account_lol'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'mmr', 'exp', 'roles', 'role', 'status', 'email', 'password'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['email_verified_at' => 'datetime'];


    public function tournaments()
    {
        return $this->hasMany(Tournament::class);
    }

    public function players()
    {
        return $this->hasMany(TournamentPlayers::class);
    }

    public function accounts($game = null)
    {
        if($game != null)
            return $this->hasMany(GamesAccount::class)->where('game', $game);
        return $this->hasMany(GamesAccount::class);
    }

    public function account_lol()
    {
        return $this->hasMany(GamesAccount::class)->where('game', 'lol');        
    }    

    public function socials()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function statistics()
    {
        return $this->hasMany(StatisticPlayers::class);
    }
}
