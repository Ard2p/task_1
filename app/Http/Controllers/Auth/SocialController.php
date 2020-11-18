<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Socialite;
use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class SocialController extends Controller
{
    public function redirect($provider)
    {
        $provider = strtolower($provider);
        return Socialite::with($provider)->redirect();
    }

    public function callback($provider)
    {
        $provider = strtolower($provider);
        $socialAccount = Socialite::driver($provider)->stateless()->user();
        // try {
        //     $socialAccount = Socialite::driver($provider)->stateless()->user();
        // } 
        // catch (\GuzzleHttp\Exception\ClientException $e) {
        //     return abort(401);
        // }

        if($provider == 'vkontakte'){
            $socialAccount->email =
                isset($socialAccount->accessTokenResponseBody['email']) ?
                $socialAccount->accessTokenResponseBody['email'] : null;
        }

        try {
            $user = $this->setOrGetUser($provider, $socialAccount);
            \Auth::login($user);
            return redirect('/');
            // back();
        } catch (\Throwable $e) {
            return abort(500);
        }
    }

    public function logout(){
        \Auth::logout();
        Session::flush();
        return redirect('/');
    }

    public function setOrGetUser($provider, $socialAccount)
    {
        $account = SocialAccount::whereProvider($provider)
                                ->whereProviderUserId($socialAccount->getId())
                                ->first();
        if ($account) {
            return $account->user;
        } else {
            $account = new SocialAccount([
                'provider_user_id' => $socialAccount->getId(),
                'provider' => $provider
            ]);
            if (\Auth::check())
                $user = \Auth::user();
            else {
                $user = $socialAccount->getEmail() ? User::whereEmail($socialAccount->getEmail())->first() : null;
                if (!$user) {
                    $user = User::create([
                        'email'         => $socialAccount->getEmail(),
                        'password'      => \Hash::make(\Str::random(8))
                    ]);
                }
            }
            $account->user()->associate($user);
            $account->save();
            return $user;
        }
    }
}
