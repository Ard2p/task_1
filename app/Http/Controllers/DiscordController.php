<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StatisticPlayers;
use App\Models\User;
use App\Models\Tournament;
use App\Models\TournamentPlayers;
use App\Models\GamesAccount;


class DiscordController extends Controller
{
  public function index()
  {
    set_time_limit(0);
    // dd(phpinfo());

    $this->check_raiting();


    // $statistics = StatisticPlayers::with('account')->where('game', 'lol')->with('user')ж
    // $statistics = StatisticPlayers::where('user_id', 53)->first();
    // // $statistics = TournamentPlayers::where('user_id', 53)->get();
    // // $statistics->streak = 3;
    // $statistics->k -= 5;
    // $statistics->d -= 5;
    // $statistics->a -= 7;
    // $statistics->win -= 1;
    // $statistics->save();

    // $tournament = Tournament::find(50);
    // $tournament->type = 'rtc_playoff';
    // $tournament->save();

    // $tournament = Tournament::whereIn('id', [27,28,29])->get();
    // dd($tournament );
    // $user = User::where('id', 196)->first();
    // // $user->status = 'ban';
    // $user->mmr = 2200;
    // $user->save();

    // $user = User::where('id', 3)->first();
    // // $user->status = 'ban';
    // $user->mmr = 2700;
    // $user->save();

    // $user = User::where('id', 12)->first();
    // // $user->status = 'ban';
    // $user->mmr = 1400;
    // $user->save();

    // $user = User::where('id', 3)->first();
    // // $user->status = 'ban';
    // $user->mmr = 2700;
    // $user->save();

    // 196 2200
    // 53 2000
    // 12 1400
    // 3  2700

    // dd($statistics );
    return view('page.discord.index');
  }

  public function push()
  {
    return view('page.discord.index');
  }

  public function check_raiting()
  {
    $tournaments = Tournament::where('teams', '!=', null)->where('id', '>=', 346)
    // ->limit(300)
    ->get();

    foreach ($tournaments as $tournament) {
      $teams = json_decode($tournament->teams);
      foreach ($teams as $team) {
        $codes[$team->code] = null;
      }
    }
   	
		$riot = $this->riotAPI();
		
  	// //Замененные коды
		// // $codes['RU04854-d990a8e7-d045-45ec-9edf-aaf3e867c479'] = null;
		// // $codes['RU04856-184d2462-9f48-40f0-bfd4-26d5889741b2'] = null;

    foreach ($codes as $code => $match_id) {

      try {
        $match_id = $riot->getMatchIdsByTournamentCode($code)[0];
      } catch (\Exception $e) {
        // $codes[$code] = $e['code'];
      }

      if (!$match_id) continue;
      $codes[$code] = $match_id;

      try {
        $codes[$code] = $match = $riot->getMatchByTournamentCode($match_id, $code);
      } catch (\Exception $e) {
        // if($e['code'] == 404)
        $match = false;

        // if($e['code'] == 504)
        //   $match = $riot->getMatchByTournamentCode($match_id, $code);
        
        // dd([$e, $code, $match_id, $match]);
			}
			
			

			if (!$match) continue;	
		
      foreach ($match->participants as $v) {
        $player = $match->participantIdentities[array_search(
          $v->participantId,
          array_column($match->participantIdentities, 'participantId')
				)]->player;	

				$profileId = $player->summonerId;				

			 if (!isset($players[$profileId])) $players[$profileId] = ['win' => 0, 'lose' => 0, 'k' => 0, 'd' => 0, 'a' => 0];	 

        $players[$profileId]['win']   = $v->stats->win ? $players[$profileId]['win'] + 1 : $players[$profileId]['win'];
        $players[$profileId]['lose']  = $v->stats->win ? $players[$profileId]['lose']    : $players[$profileId]['lose'] + 1;
        $players[$profileId]['k']     = $players[$profileId]['k'] + $v->stats->kills;
        $players[$profileId]['d']     = $players[$profileId]['d'] + $v->stats->deaths;
        $players[$profileId]['a']     = $players[$profileId]['a'] + $v->stats->assists;
			}
		
    }
		// print_r($players);
		// dd($players);
		// // dd();
    $users = GamesAccount::whereIn('profileId', array_keys($players))->get();
		// dd($codes);
    foreach ($users as $user) {
			$player = $players[$user->profileId];
			$player['user_id'] = $user->user_id;
      StatisticPlayers::where('user_id', $user->user_id)->where('game', 'lol')
        ->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))
        // ->updateOrCreate($player);
        ->create($player);

    }
	}
	
}