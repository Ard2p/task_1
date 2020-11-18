<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\USocials;
use App\Models\TPlayers;

use App\Http\Controllers\Controller;
use App\Exceptions\EmailTakenException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class OAuthController extends Controller
{
	/**
	 * Redirect the user to the provider authentication page.
	 *
	 * @param  string $provider
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function redirectToProvider($provider)
	{
		// return ['url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl()];
		return redirect(Socialite::driver($provider)->stateless()->redirect()->getTargetUrl());
	}

	/**
	 * Obtain the user information from the provider.
	 *
	 * @param  string $driver
	 * @return \Illuminate\Http\Response
	 */
	public function handleProviderCallback($provider)
	{
		$user = Socialite::driver($provider)->stateless()->user();
		$user = $this->findOrCreateUser($provider, $user);

		$token = auth()->login($user);
		// $payload = auth()->payload();

		// return [
		//     'token' => $token,
		//     'token_type' => 'bearer',
		//     'expires_in' => $payload('exp') - time()
		// ];
		return back()->withCookie('token', $token, config('jwt.ttl'), "/", null, false, true);
	}

	/**
	 * @param  string $provider
	 * @param  \Laravel\Socialite\Contracts\User $sUser
	 * @return \App\User|false
	 */
	public function findOrCreateUser($provider, $socialAccount)
	{
		$account = USocials::whereProvider($provider)
			->whereProviderUserId($socialAccount->getId())->first();

		if ($account) {
			return $account->user;
		} else {
			$account = new USocials([
				'provider_user_id' => $socialAccount->getId(),
				'provider' => $provider
			]);

			if (\Auth::check())
				$user = \Auth::user();
			else {
				$user = $socialAccount->getEmail() ? User::whereEmail($socialAccount->getEmail())->first() : null;
				if (!$user) {
					$user = User::create([
						'email'             => $socialAccount->getEmail(),
						'email_verified_at' => now()
					]);
				}
			}

			$account->user()->associate($user);
			$account->save();
			return $user;
		}
	}


	public function me(Request $request)
	{
		$user = User::find(\Auth::user()->id);

		$select = [
			'tournaments.id AS tournament_id', 'name', 'tournaments.type',  'status', 'start', 'game'
		];

		$tournaments_request = TPlayers::select($select)
			->join('tournaments', 'tournaments.id', '=', 'tournaments_players.tournament_id')
			->whereIn('status', ['open', 'balance', 'process'])
			->where('tournaments_players.user_id', $user->id)
			->get();

		$user->tournaments_request = $tournaments_request;

		return response()->json([
			'status' => 'success',
			'data' => $user,
		]);
	}

	// public function logout()
	// {
	//   auth('api')->logout();
	//   return response()->json(['message' => 'Successfully logged out']);
	// }

	public function logout()
	{
		$this->guard()->logout();
		return response()->json([
			'status' => 'success',
			'message' => 'Logged out Successfully.'
		], 200);
	}

	private function guard()
	{
		return \Auth::guard();
	}
}
