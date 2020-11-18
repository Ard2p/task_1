<?php

namespace App\Http\Controllers\API\v1\Profiles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

use App\Models\Tournament;
use App\Models\TPlayers;
use App\Models\GameAccount;

class AccountsController extends Controller
{
	public function addAccount(Request $request)
	{
		switch ($request->get('event')) {
			case 'get_account':
				// return $this->getAccount($request);
				break;
			case 'check_account':
				return $this->checkAccount($request);
				break;
			case 'set_data':
				return $this->setData($request);
				break;
		}

		return response()->json(['success' => false, 'code' => 'game_account.error_undefined']);
	}

	public function getAccount($request)
	{
		$riot = $this->riotAPI();
		$user = \Auth::user();
		$game = $request->get('game');

		if ($game == '')
			return response()->json(['success' => false, 'code' => 'game_account.empty_game']);

		if ($request->get('summonername') == '')
			return response()->json(['success' => false, 'code' => 'game_account.' . $game . '.empty_summonername']);

		try {
			$summoner = $riot->getSummonerByName($request->input('summonername'));
		} catch (\Throwable $e) {
			return response()->json(['success' => false, 'code' => 'lol.summoner.' . $e->getCode()]);
		}

		$account = GameAccount::where('game', $game)->where('profileId', $summoner->id)->first();
		if ($account)
			return response()->json(['success' => false, 'code' => 'game_account.account_already']);

		$iconId = random_int(0, 28);
		if ($summoner->profileIconId == $iconId)
			$iconId = random_int(0, 28);

		session([
			$game . '.profileIconId' => $iconId,
			$game . '.summonerName'  => $summoner->name,
			$game . '.profileId'     => $summoner->id
		]);

		return response()->json(['success' => true, 'step' => 'check_account', 'set_icon' => $iconId, 'summoner_name' => $summoner->name]);
	}

	public function checkAccount($request)
	{
		$riot = $this->riotAPI();
		$user = \Auth::user();
		$game = $request->get('game');

		if ($game  == '')
			return response()->json(['success' => false, 'code' => 'game_account.empty_game']);

		if (session($game . '.profileIconId') && session($game . '.profileId'))
			return response()->json(['success' => false, 'code' => 'game_account.empty_session']);

		try {
			$summoner = $riot->getSummoner(session($game . '.profileId'));
		} catch (\Throwable $e) {
			return response()->json(['success' => false, 'code' => 'lol.summoner.' . $e->getCode()]);
		}

		if ($summoner->profileIconId != session($game . 'profileIconId'))
			return response()->json(['success' => false, 'code' => 'lol.icon_mismatch']);

		// $user->accounts()->create([
		//   'game'      => $game,
		//   'nickname'  => $summoner->name,
		//   'profileId' => $summoner->id,
		//   'accountId' => $summoner->accountId,
		//   'active'    => true,
		// ]);

		session()->forget([$game . '.profileIconId', $game . '.summonerName', $game . '.profileId']);
		return response()->json(['success' => true, 'step' => 'set_data', 'league' => config('games.lol.leagues')]);
	}

	public function setData($request)
	{
		// $riot = $this->riotAPI();
		// $game = session('game.addAccount');
		// $user = \Auth::user();

		// $summoner = $riot->getSummoner(session($game . 'profileId'));

		// if ($summoner->profileIconId != session($game . 'profileIconId'))
		//   return response()->json(['success' => false, 'code' => 'lol.icon_mismatch']);

		// $user->accounts()->create([
		//   'game'      => $game,
		//   'nickname'  => $summoner->name,
		//   'profileId' => $summoner->id,
		//   'accountId' => $summoner->accountId,
		//   'active'    => true,
		// ]);

		// session()->forget(['game.addAccount', $game . '.profileIconId', $game . '.summonerName', $game . '.profileId']);

		return response()->json(['success' => true, 'step' => 'end']);
	}
}
