<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Tournament;
use App\Models\TournamentPlayers;
use App\Models\StatisticPlayers;
use App\Models\GamesAccount;
use App\Models\User as Users;

use RiotAPI\LeagueAPI\LeagueAPI;
use RiotAPI\LeagueAPI\Objects;

use Zzepish\Service\RolesBalancer;
use Zzepish\Service\TeamsFormer;

use Zzepish\Entity\UsersByRole;
use Zzepish\Entity\MmrTier;
use Zzepish\Entity\Role;
use Zzepish\Entity\User;


class TournamentsController extends Controller
{
  function __construct()
  {
    // $this->middleware('CheckPerm:moder')->except('quizDetails');
    $this->middleware('CheckPerm:admin')->only('destroy');
    $this->middleware('CheckPerm:streamer')->only('create', 'store', 'update');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    if (\Auth::check())
      $where = in_array(\Auth::user()->role, ['dev', 'admin', 'moder']) ? ['arhive'] : ['create', 'arhive'];
    else $where = ['arhive'];
    $tournaments = Tournament::whereNotIn('status', $where)->get();
    return view('page.tournaments.index', compact('tournaments'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('page.tournaments.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {

    $validator = $request->validate([
      'name'      => 'required|max:255',
      'img'       => 'required|mimes:jpeg,bmp,png,gif|dimensions:ratio=16/9|max:2048',    // webm
      'desc'      => 'nullable|string',
      'twitch'    => 'nullable|string|max:30',                                            // url
      'game'      => 'required|string',
      'type'      => 'required|string',
      'status'    => 'required|in:create,pending,open'
      // 'status'    => 'required|in:create,pending,open,balance,process,end,arhive'
    ]);

    // try {
    $validator['img'] = \Storage::putFile('tournaments/preview', $request->file('img'));
    // } catch (\Exception $e) {
    //   dd($e);
    // }
    // dd($request);
    // dd('');
    // if ($validator->fails()) {
    //     return redirect()->route('tour.create')
    //         ->withErrors($validator, 'tournament');
    // }

    \Auth::user()->tournaments()->create($validator);
    return redirect()->route('tour.index')->with('success', 'Show is successfully saved');
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Tournament  $tournament
   * @return \Illuminate\Http\Response
   */
  public function show($game, $type, $id)
  {
    $tournament = Tournament::findOrFail($id);
    $tournament->teams = $tournament->teams != null ? json_decode($tournament->teams, true) : null;

    // dd( $tournament->teams);

    $players = $tournament->players;
    $teams = [0 => [], -1 => []];

    // $players = $tournament->players()
    //     ->join('games_accounts as a', 'a.user_id', '=', 'tournaments_players.user_id')->get();

    $team = null;
    foreach ($players as $player) {
      // dump($player->user_id, \Auth::user()->id, $player->team);
      if ($player->user_id === \Auth::user()->id)
        $team = (int) $player->team;

      if ($player->team <= 0)
        $teams[$player->team][] = $player;
      else
        $teams[$player->team][$player->role] = $player;
    }
    // dd($byTeams['teams']); 
    if ($tournament->type == 'rtc_playoff')
      return view('page.tournaments.show_rtc_playoff', compact('tournament', 'teams', 'team'));
    else
      return view('page.tournaments.show', compact('tournament', 'teams', 'team'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Tournament  $tournament
   * @return \Illuminate\Http\Response
   */
  public function edit()
  {
    // $show = Show::findOrFail($id);

    // return view('edit', compact('show'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Tournament  $tournament
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request)
  {
    // dd($request->all());
    if ($request->input('action') == 'tour_status') {

      $validator = \Validator::make($request->only('status'), [
        'status' => 'required|in:create,pending,open,balance,process,end,arhive'
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 200);
      }

      $tournament = Tournament::find($request->input('id'));
      $tournament->status = $request->input('status');

      if ($request->input('status') == 'open') {


        if ($tournament->game == 'lol' && $tournament->provider_id  == NULL) {
          $riot = $this->riotAPI();

          $tournamentParams = new Objects\TournamentRegistrationParameters([
            'providerId' => config('games.lol.provider'),
            'name'       => "FF15"
            // $tournament->name
          ]);
        
          $provider_id = $riot->createTournament($tournamentParams);
          $tournament->provider_id = $provider_id;
        }
      }

      if ($request->input('status') == 'balance') {
        $teams = $this->balance($tournament);

        if ($tournament->type == 'rtc_playoff')
          $tournament->teams = [0 => $teams];
        else
          $tournament->teams = $teams;
      }

      $tournament->save();

      return response()->json(['status' => 'success', 'code' => $tournament->status, 'id' => $tournament->id], 200);
    }
    // $validator = $request->validate([
    //     'name'      => 'required|max:255',
    //     'img'       => 'nullable|mimes:jpeg,bmp,png,gif,webm|dimensions:ratio=16/9|max:2048',
    //     'desc'      => 'nullable|string',
    //     'twitch'    => 'nullable|url',
    //     'game'      => 'required',
    //     'type'      => 'required',
    //     'status'    => 'required'
    // ]); 

  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Tournament  $tournament
   * @return \Illuminate\Http\Response
   */
  public function destroy()
  {
    // $show = Show::findOrFail($id);
    // $show->delete();

    // return redirect('/shows')->with('success', 'Show is successfully deleted');
  }

  public function reg(Request $request)
  {
    $user = \Auth::user();
    $regUserTours = $request->get('regUserTours');
    $tour_id = $request->only('id');

    $data['status']   = 'error';
    $data['code']     = 'error_reg';

    if ($request->input('action') == 'tour_enter') {
      $allow_status = ['open', 'balance'];
      $tournament = Tournament::findOrFail($tour_id)->first();

      if (in_array($tournament->status, $allow_status)) {

        $leagues = config('games.lol.leagues');   
        if ($user->mmr < $leagues['silver']['division'][4])
          return response()->json(['messange' => 'Вы должны быть не ниже Сильвер IV!'], 200);

        if ($regUserTours->has($tournament->id))
          return response()->json(['messange' => 'Вы уже учавствуете в этом турнире!'], 200);

        if ($tournament->type == 'rtc' && $regUserTours->firstWhere('type', 'rtc'))
          return response()->json(['messange' => 'Вы можете быть только в одном РТК одновремменно!'], 200);

        $account = $user->accounts()->where(['game' => $tournament->game])->first();
        $reg = $tournament->players()->create([
          'user_id'       => $user->id,
          'nickname'      => $account->nickname,
          'roles'         => $user->roles,
          'mmr'           => $user->mmr ? $user->mmr : 0,
          'priority'      => 0,
          'team'          => 0,
          'profileId'     => $account->profileId,
          'accountId'     => $account->accountId
        ])->get('tournament_id');

        if ($reg) {
          $data['code']     = 'exit';
          $data['status']   = 'success';
          $data['btn_name'] = __('page.tour.btn-reg.exit');
          return response()->json($data, 200);
        }
      } else {
        $data['messange'] = 'Турнир еще не доступен для регистрации!';
      }
    }

    if ($request->input('action') == 'tour_exit') {
      $allow_status = [];

      if ($regUserTours->has($tour_id)) {

        $tournament = Tournament::findOrFail($tour_id)->first();
        if (in_array($tournament->status, $allow_status)) {
          if ($user->players()->where('tournament_id', $tour_id)->delete()) {
            $data['code']     = 'enter';
            $data['status']   = 'success';
            $data['btn_name'] = __('page.tour.btn-reg.enter');
            return response()->json($data, 200);
          }
        } else {
          $data['messange'] = 'Нельзя выйти на этапе балансировки!';
        }
      } else {
        $data['messange'] = 'Вы не участник этого турнира!';
      }
    }

    return response()->json($data, 200);
  }

  public function callback(Request $request, $game)
  {
    if ($game == 'lol') {
      $this->data = $request->json()->all();
      $metaData = json_decode($this->data['metaData']);

      if ($metaData->yek == 'AAe%xFeMRMEByo8NuQiXaksmny#T4G{{') {
        $riot  = $this->riotAPI();
        $match = $riot->getMatchByTournamentCode($this->data['gameId'], $this->data['shortCode']);
        $tournament   = Tournament::find($metaData->tournament_id);

        // $winTeamKey   = array_search('Win', array_column($match->teams, 'win'));
        // $winTeamId    = $match->teams[$winTeamKey]->teamId;
        // $loseTeamId   = $winTeamId == 100 ? 200 : 100;

        foreach ($match->participants as $v) {
          $player = $match->participantIdentities[array_search(
            $v->participantId,
            array_column($match->participantIdentities, 'participantId')
          )]->player;

          $pid = $player->summonerId;
          $participants[$pid]['pid'] = $player->summonerId;
          $participants[$pid]['id']  = $v->participantId;
          $participants[$pid]['win'] = $v->stats->win ? 1 : 0;
          $participants[$pid]['k']   = $v->stats->kills;
          $participants[$pid]['d']   = $v->stats->deaths;
          $participants[$pid]['a']   = $v->stats->assists;
        }

        $players = GamesAccount::whereIn('profileId', array_column($participants, 'pid'))->get();
        foreach ($players as $player) {
          $pid = $player->profileId;
          $exp = 150;
          $mmrIncrement = 0;
          $statisticsData = [];
          $statisticsData['user_id'] = $player->user_id;
          $statisticsData['game']    = 'lol';

          $statistics = StatisticPlayers::where('user_id', $player->user_id)->where('game', 'lol')
            ->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->first();

          if ($participants[$pid]['win']) {
            if ($player->streak > 0) $player->streak++;
            else $player->streak = 1;

            if ($player->streak % 3 == 0) $mmrIncrement = 50;

            $statisticsData['win'] = $statistics ? $statistics->win + 1 : 1;
            // $exp += $player->streak * 10;
            $exp = 250;
          } else {
            if ($player->streak > 0) $player->streak = -1;
            else $player->streak--;

            if ($player->streak <= -2) $mmrIncrement = -50;

            $statisticsData['lose'] = $statistics ? $statistics->lose + 1 : 1;
          }

          $leagues = config('games.lol.leagues');
          $player->save();
          $user = Users::find($player->user_id);
          $user->mmr += $mmrIncrement;
          $user->mmr = $user->mmr > 2700 ? 2700 : $user->mmr;
          $user->mmr = $user->mmr < $leagues['silver']['division'][1] ? $leagues['silver']['division'][1] : $user->mmr;
          $user->exp += $exp;
          $user->save();
          
        
          $statisticsData['k'] = $statistics ? $statistics->k + $participants[$pid]['k'] : $participants[$pid]['k'];
          $statisticsData['d'] = $statistics ? $statistics->d + $participants[$pid]['d'] : $participants[$pid]['d'];
          $statisticsData['a'] = $statistics ? $statistics->a + $participants[$pid]['a'] : $participants[$pid]['a'];

          if (!$statistics)
            $statistics = StatisticPlayers::create($statisticsData);
          else
            StatisticPlayers::where('user_id', $player->user_id)->where('game', 'lol')
            ->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->update($statisticsData);
        }

        $this->nicknames = array_map(function ($a) {
          return $a['summonerName'];
        }, $this->data['winningTeam']);

        if ($tournament) {
          $plaeyrs = $tournament->players()->whereIn('nickname', $this->nicknames)->get();

          foreach ($plaeyrs as $player) {
            if ($player->team != 0)
              $teamIndex = (int) $player->team - 1;
          }


          // if ($tournament->type == 'rtc_playoff') 
          //   $teams = json_decode($tournament->teams[array_key_last($tournament->teams)]); 
          // else
            $teams = json_decode($tournament->teams);

          // foreach($teams as $team){
          //     if($team->team == $metaData->team_id)
          //         $team->win = true;     
          // }

          $teams->{$teamIndex}->win = true;

          // if ($tournament->type == 'rtc_playoff') {
          //   $tournament->teams[array_key_last($tournament->teams)] = $teams;
          //   $teams = $tournament->teams;
          // }

          $tournament->teams = json_encode($teams);
          $tournament->save();

          return response()->json('ok', 200);
        }
      }
    }
    return response()->json('error', 401);
  }

  protected function balance($tournament)
  {
    $players = $tournament->players->keyBy('user_id');
    if (count($players)) {

      $roles_list = [
        'sup'   => new Role('sup'),
        'adc'   => new Role('adc'),
        'top'   => new Role('top'),
        'mid'   => new Role('mid'),
        'jung'  => new Role('jung')
      ];

      $usersByRoles = [];
      foreach ($roles_list as $role_name => $role) {
        $usersByRoles[$role_name] = new UsersByRole($role);
      }

      foreach ($players as $player) {
        if ($player->team == -1)
          continue;
          

        $priority  = 0;
        // $priority  = $player->user_id == 3    ?  100  : $priority;
        // $priority  = $player->user_id == 517  ? -100  : $priority;         


        // $player->mmr = $player->user_id == 3   ? 2500 : $player->mmr;
        // $player->mmr = $player->user_id == 517 ? 2500 : $player->mmr;


        $player->roles = json_decode($player->roles);
        $usersByRoles[$player->roles[0]]->addUser(new User($player->mmr, [
          $roles_list[$player->roles[0]],
          $roles_list[$player->roles[1]],
          $roles_list[$player->roles[2]],
          $roles_list[$player->roles[3]],
          $roles_list[$player->roles[4]],
        ], $player->user_id, $priority));
      }

      $rolesBalancer  = new RolesBalancer(count($players), $roles_list);
      $usersByRoles   = $rolesBalancer->getBalancedUsersByRoles($usersByRoles);





      foreach ($usersByRoles as $key => $userByRoles) {
        $collection = collect($userByRoles->getUsers());
        $sorted = $collection->sortByDesc(function ($user, $key) {
          return $user->getPriority();
        })->all();
        while (count($sorted) > (int) (count($players) / 5)) {
          end($sorted);
          unset($sorted[key($sorted)]);
        }
        $usersByRoles[$key]->setUsers($sorted);
      }





      $teamsFormer    = new TeamsFormer($usersByRoles);

      if ($tournament->type == 'rtc_playoff')
        $formedTeams  = $teamsFormer->formTeams(true);
      else
        $formedTeams  = $teamsFormer->formTeams(false);


      foreach ($players as $player) {
        $player->team = 0;
        $player->role = NULL;
      }


      $riot = $this->riotAPI();



      $playTeams = collect();
      $code_num = 0;
      $codes = [];
      foreach ($formedTeams[0] as $k => $team) {

        if ($tournament->provider_id != null)
          if ($code_num == (int)floor($k / 2)) {
            $tournamentParams = new Objects\TournamentCodeParameters([
              "mapType"       => "SUMMONERS_RIFT",
              "metadata"      => json_encode([
                'title'         => $tournament->name,
                'yek'           => 'AAe%xFeMRMEByo8NuQiXaksmny#T4G{{',
                'tournament_id' => $tournament->id,
                'provider_id'   => $tournament->provider_id
              ]),
              "pickType"      => "TOURNAMENT_DRAFT",
              "spectatorType" => "ALL",
              "teamSize"      => 5
            ]);
            // dump($tournament->provider_id, $tournamentParams);
            if ($tournament->type == 'rtc_playoff')
              $codes[$code_num] = [null];
            else
              $codes[$code_num] = $riot->createTournamentCodes($tournament->provider_id, 1, $tournamentParams);
            $code_num++;
          }

        $playTeams->push([
          'team' => $k + 1, 'mmr' => $team->getAverageMmr(), 'code' => $codes[(int)floor($k / 2)][0]
        ]);
        foreach ($team->getUsers() as $role => $user) {
          $players[$user->getUserIdentifier()]->team = $k + 1;
          $players[$user->getUserIdentifier()]->role = $role;
        }
      }

      $tournament->players()->saveMany($players);

      $lobby = $playTeams->sortByDesc(function ($team, $key) {
        return $team['mmr'];
      });

      if ($tournament->type == 'rtc_playoff') {
        $count = count($playTeams);

        for ($i = 1; $i < 10; $i++) {
          if (2 ^ $i < $count && $count >= 2 ^ $i + 1){
            $stage = (int)ceil($count / 2 ^ $i) * (2 ^ $i / 2);
            break;
          }
        }

        // $codes = $this->createTournamentCodes($tournament, $stage);

        $lobby_temp = array();

        $stage = 16;

        for ($i = 0; $i < $stage; $i++) {
          $lobby_temp[$i * 2] = $playTeams[$i];
          // $lobby_temp[$i * 2]['code'] = $codes[$i];

          if (isset($playTeams[$stage + $i])) {
            $lobby_temp[$i * 2 + 1] = $playTeams[$stage + $i];
            // $lobby_temp[$i * 2 + 1]['code'] = $codes[$i];
          } else {
            $lobby_temp[$i * 2 + 1] = ['team' => NULL, 'mmr' => NULL, 'code' => NULL];
            $lobby_temp[$i * 2]['win'] = true;
            $lobby_temp[$i * 2]['code'] = NULL;
          }
        }

        $lobby = $lobby_temp;
      }

      // $playTeams->sortByDesc('mmr');
      return $lobby;
    }
    return null;
  }

  // protected function createTournamentCodes($tournament, $count){  
  //   if ($tournament->provider_id != null) {
  //     $tournamentParams = new Objects\TournamentCodeParameters([
  //       "mapType"       => "SUMMONERS_RIFT",
  //       "metadata"      => json_encode([
  //         'title'         => $tournament->name,
  //         'yek'           => 'AAe%xFeMRMEByo8NuQiXaksmny#T4G{{',
  //         'tournament_id' => $tournament->id,
  //         'provider_id'   => $tournament->provider_id
  //       ]),
  //       "pickType"      => "TOURNAMENT_DRAFT",
  //       "spectatorType" => "ALL",
  //       "teamSize"      => 5
  //     ]);
  //     $riot = $this->riotAPI();
  //     $codes = $riot->createTournamentCodes($tournament->provider_id, $count, $tournamentParams);  
  //     return $codes;
  //   }
  // }

}

