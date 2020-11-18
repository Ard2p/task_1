<?php

namespace App\Http\Controllers\API\v1\Tournaments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

use App\Models\Tournament;
use App\Models\TPlayers;
use App\Models\GameAccount;

class EventsController extends Controller
{
  public function switch(Request $request, $id)
  {
    switch ($request->get('event')) {
      case 'enter':
        return $this->enter($request, $id);
        break;
      case 'leave':
        return $this->leave($request, $id);
        break;
    }
    return response()->json(['success' => false, 'code' => 'tournament.error_undefined']);
  }

  public function enter($request, $id) // inaccessible
  {
    $user = \Auth::user();
    $allow_status = ['open', 'balance'];

    $tournament = Tournament::find($id);
    if (!$tournament)
      return response()->json(['success' => false, 'code' => 'tournament.not_found']);

    if (!in_array($tournament->status, $allow_status))
      return response()->json(['success' => false, 'code' => 'tournament.wrong_enter_status']);

    $account = GameAccount::where('game', $tournament->game)->where('user_id', $user->id)->where('active', true)->first();
    if (!$account)
      return response()->json(['success' => false, 'code' => 'tournament.no_account', 'game' => $tournament->game]);

    $player = TPlayers::where('tournament_id', $tournament->id)->where('user_id', $user->id)->first();
    if ($player)
      return response()->json(['success' => false, 'code' => 'tournament.enter_already']);

    $select = ['tournaments.id AS tournament_id', 'name', 'tournaments.type',  'status', 'start', 'game'];
    $tournaments_request = TPlayers::select($select)
      ->join('tournaments', 'tournaments.id', '=', 'tournaments_players.tournament_id')
      ->whereIn('status', ['open', 'balance', 'process'])
      ->where('tournaments_players.user_id', $user->id)
      ->get();

    $filtered = $tournaments_request->where('type', 'rtc')->count();
    if ($tournament->type == 'rtc' && $filtered > 0)
      return response()->json(['success' => false, 'code' => 'tournament.more_type_request']);

    $enter = $tournament->players()->create([
      'user_id'       => $user->id,
      'account_id'    => $account->id
    ]);

    if ($enter) {    
      $tournaments_request->push([    
        'tournament_id' => $tournament->id,    
        'name'          => $tournament->name,
        'type'          => $tournament->type,
        'status'        => $tournament->status, 
        'start'         => $tournament->start,
        'game'          => $tournament->game
      ]);
      return response()->json(['success' => true, 'tournaments_request' => $tournaments_request]);
    }
  }

  public function leave($request, $id)
  {
    $user = \Auth::user();
    $allow_status = ['open'];

    $tournament = Tournament::find($id);
    if (!$tournament)
      return response()->json(['success' => false, 'code' => 'tournament.not_found']);

    if (!in_array($tournament->status, $allow_status))
      return response()->json(['success' => false, 'code' => 'tournament.wrong_leave_status']);

    $player = TPlayers::where('user_id', $user->id)->where('tournament_id', $tournament->id);
    if (!$player)
      return response()->json(['success' => false, 'code' => 'tournament.not_registred']);

    $leave = $player->delete();

    if ($leave) {
      $select = ['tournaments.id AS tournament_id', 'name', 'tournaments.type',  'status', 'start', 'game'];
      $tournaments_request = TPlayers::select($select)
        ->join('tournaments', 'tournaments.id', '=', 'tournaments_players.tournament_id')
        ->whereIn('status', ['open', 'balance', 'process'])
        ->where('tournaments_players.user_id', $user->id)
        ->get();

      return response()->json(['success' => true, 'tournaments_request' => $tournaments_request]);
    }
  }
}
