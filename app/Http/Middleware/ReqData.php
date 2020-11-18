<?php

namespace App\Http\Middleware;

use Closure;
use Debugbar;

class ReqData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */    
    public function handle($request, Closure $next)
    {
        if(\Auth::check()){         
            $checks = ['nickname', 'mmr', 'roles'];

            $user = \Auth::user();

            if ($user->status == "ban")
                return response()->view('page.ban');

            $account = $user->accounts()->firstOrCreate([
                'game' => 'lol'
            ], [
                'user_id' => $user->id,
                'game' => 'lol',
                'verifyCode' => \Str::random(8)
            ]);

            if($account->nickname != null)
                $request->attributes->add(['nickname' => $account->nickname]);
            $request->attributes->add(['verifyCode' => $account->verifyCode]);
            $request->attributes->add(['roles' => $user->roles]);
            $request->attributes->add(['mmr' => $user->mmr]);

            $data = array(
                'status' => 'error',
                'message' => 'Вы не закончили регистрацию! Вы не можете взаимодействовать с сайтом!'
            );

            foreach ($checks as $check)
                if($request->get($check) == NULL){
                    $data['inputs'][$check] = true;
                    $request->attributes->add(['modalRegForm' => true]);
                } else {
                    $data['inputs'][$check] = false;
                }
            $request->attributes->add(['modalRegFormData' => $data['inputs']]);

            if($request->get('modalRegForm')){
                if ($request->isMethod('post'))
                    if (!$request->is('profile'))
                        return response()->json($data, 403);
                view()->share('profileIconId',  session('profileIconId'));
                view()->share('leagues',        config('games.lol.leagues'));
                view()->share('modalRegForm',   true);
            }
         
            $regUserTours = \Auth::user()->players()               
                ->join('tournaments as t', 't.id', '=', 'tournaments_players.tournament_id') 
                ->select('t.id', 't.name', 't.game', 't.type', 't.status', 'team')
                ->whereIn('t.status', ['pending','open','balance','process'])->get()->keyBy('id'); 
            view()->share(compact('regUserTours'), compact('account')); 
               
            // $request->attributes->add(['account'        => $account]);   
            $request->attributes->add(['regUserTours'   => $regUserTours]);
        }
        return $next($request);
    }
}
