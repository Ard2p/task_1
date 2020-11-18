<?php

namespace App\Http\Controllers\API\v1\Tournaments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

use App\Models\Tournament;
use App\Models\TPlayers;

class TournamentsController extends Controller
{

  protected $select = [
    'id', 'user_id', 'name', 'img', 'desc', 'twitch', 'discord',
    'game', 'type', 'lvl', 'max_players', 'start', 'status'
  ];

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $period = $request->get('period') ? $request->get('period') : 1;

    $tournaments = Tournament::select($this->select)
    // ->where('start', '<=', Carbon::now()->addDays($period))
    ->paginate(6);

    return $tournaments;
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    if (Gate::denies('tournaments@create'))
      return response()->json(['success' => false], 403);

      $validator = \Validator::make($request->all(), [
        'name'        => 'required|max:255',
        // 'img'       => 'required|mimes:jpeg,bmp,png,gif|dimensions:ratio=16/9|max:2048',    // webm
        'desc'        => 'nullable|string',
        'twitch'      => 'nullable|string|max:100',
        'discord'     => 'nullable|string|max:100',                                            // url

        'game'        => 'required|string',
        'type'        => 'required|string',

        'lvl'         => 'nullable|integer',
        'max_players' => 'nullable|integer',

        'start'       => 'required|string',
        'status'      => 'required|in:create,pending,open'
      ]);
    // $validator['img'] = \Storage::putFile('tournaments/preview', $request->file('img'));

    if ($validator->fails())
      return response()->json($validator->errors());

    // \Auth::user()->tournaments()->create($request->all());

    $tournament = new Tournament($request->only([
      'name', 'desc', 'twitch','discord', 'game', 'type', 'lvl', 'max_players', 'start', 'status'
    ]));
    $tournament->user_id = \Auth::user()->id;
    $tournament->save();

    return Tournament::select($this->select)->find($tournament->id);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $select = [
      'tournaments_players.id', 'tournaments_players.user_id', 'tournaments_players.role',
      'account_id', 'team', 'nickname', 'streak', 'avatar', 'exp', 'mmr', 'users.role AS site_role'];

    if (Gate::allows('tournaments@playersInfo'))
      $select = array_merge($select, ['priority', 'roles', 'is_beta']);

    $players = TPlayers::select($select)
      ->join('users',          'users.id',                '=', 'tournaments_players.user_id')
      ->join('games_accounts', 'games_accounts.id',       '=', 'tournaments_players.account_id')
      ->join('games_profiles', 'games_profiles.user_id',  '=', 'tournaments_players.user_id')
      ->where('tournaments_players.tournament_id', $id)->where('games_accounts.active', true)->get();

    $tournament = Tournament::select($this->select)->find($id);
    $tournament->players = $players;
    return $tournament;
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $tournament = Tournament::find($id);

    if (Gate::denies('tournaments@edit', $tournament))
      return response()->json(['success' => false], 403);

    $validator = \Validator::make($request->all(), [
      'name'        => 'required|max:255',
      // 'img'       => 'required|mimes:jpeg,bmp,png,gif|dimensions:ratio=16/9|max:2048',    // webm
      'desc'        => 'nullable|string',
      'twitch'      => 'nullable|string|max:30',                                            // url

      'game'        => 'required|string',
      'type'        => 'required|string',

      'lvl'         => 'nullable|integer',
      'max_players' => 'nullable|integer',

      'start'       => 'required|string',
      'status'      => 'required|in:create,pending,open'
    ]);
    // $validator['img'] = \Storage::putFile('tournaments/preview', $request->file('img'));

    if ($validator->fails())
      return response()->json($validator->errors());

    $tournament->save();
    return $tournament;
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $tournament = Tournament::find($id);
    if (Gate::denies('tournaments@edit', $tournament))
      return response()->json(['success' => false], 403);

    $tournament->delete();
    return response()->json(['success' => true]);
  }
}
