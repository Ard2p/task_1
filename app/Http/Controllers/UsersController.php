<?php

namespace App\Http\Controllers;
use App\Models\GamesAccount;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Session;

use Debugbar;

class UsersController extends Controller
{
    function __construct (){
        // $this->middleware('CheckPerm:moder')->except('quizDetails');
        $this->middleware('CheckPerm:moder')->only('profiles');
    }

    public function profile(Request $request)
    {
        $socials  = ['vkontakte' => '', 'twitch' => ''];
        $socials  = \Auth::user()->socials->keyBy('provider');       
        $accounts = \Auth::user()->accounts;
        return view('page.users.profile', compact('accounts')
        ,compact('socials')
    );
    }

    public function profiles(Request $request)
    {
        $accounts = GamesAccount::where('nickname','<>', 'null')->orderBy('user_id')->with('user')->get();  
        return view('page.users.profiles', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = \Auth::user();
        $riot = $this->riotAPI();

        $data = [
            'status'    => 'success',
            'messange'  => 'ok',
            'code'      => '200',
            'errors'    => null
        ];

        switch ($request->input('action')){

            //Проверка саммонера, кода и сохранение в базе
            case 'verifyAccount':  
                if($user->nickname == null && session('profileIconId') == null){

                    if ($request->input('summonername') == ''){
                        $data['status']   = 'error';
                        $data['errors'][] = ['code' => 'summoner.empty','messange' => __('riot.summoner.empty')];
                    } else try {
                        $summoner = $riot->getSummonerByName($request->input('summonername'));

                        $iconId = random_int(0, 28);
                        if($summoner->profileIconId == $iconId){
                            $iconId = random_int(0, 28);
                        }                        
                        session(['profileIconId' => $iconId]);
                        session(['summonerName'  => $summoner->name]);

                        $data['status']     = 'success';
                        $data['messange']   = 'setIcon';
                        $data['code']       = $iconId;

                    } catch (\Throwable $e) {
                        $data['status'] = 'error';
                        $data['errors'][] = ['code' => 'summoner.' . $e->getCode(),'messange' => __('riot.summoner.' . $e->getCode())];
                    }

                } else if($user->nickname == null && session('profileIconId') != null){

                    try {
                        $summoner = $riot->getSummonerByName(session('summonerName'));
                        $iconId   = session('profileIconId');
                        // dd($summoner->profileIconId, $iconId);

                        if($summoner->profileIconId == $iconId){
                             
                            $user->accounts()->where(['game' => 'lol'])->first()->update([
                                'nickname'  => $summoner->name,
                                'profileId' => $summoner->id,
                                'accountId' => $summoner->accountId
                            ]);

                            $data['status']     = 'success';
                            $data['messange']   = 'verified';

                        } else {
                            $data['status'] = 'error';
                            $data['errors'][] = ['code' => 'icon.mismatch','messange' => __('riot.icon.mismatch')];
                        }
                        
                    } catch (\Throwable $e) {
                        $data['status'] = 'error';
                        $data['errors'][] = ['code' => 'summoner.' . $e->getCode(),'messange' => __('riot.summoner.' . $e->getCode())];
                    }
                    
                // } else if ($user->nickname == null){
                //     if ($request->input('summonername') == ''){
                //         $data['status'] = 'error';
                //         $data['errors'][] = ['code' => 'summoner.empty','messange' => __('riot.summoner.empty')];
                //     } else try {
                //         $summoner = $riot->getSummonerByName($request->input('summonername'));
                //         try {
                //             $code = $riot->getThirdPartyCodeBySummonerId($summoner->id);
                //             // $data['errors'][] = ['code' => $code, 'verifyCode' => $request->get('verifyCode')];
                //             if($code != $request->get('verifyCode')){
                //                 $data['status'] = 'error';
                //                 $data['errors'][] = ['code' => 'code.mismatch','messange' => __('riot.code.mismatch')];
                //             } else
                //                 $user->accounts()->where(['game' => 'lol'])->first()->update([
                //                     'nickname'  => $summoner->name,
                //                     'profileId' => $summoner->id,
                //                     'accountId' => $summoner->accountId
                //                 ]);
                //         } catch (\Throwable $e) {
                //             $data['status'] = 'error';
                //             $data['errors'][] = ['code' => 'code.code.' . $e->getCode(), 'messange' => __('riot.code.' . $e->getCode())];
                //         }
                //     } catch (\Throwable $e) {
                //         $data['status'] = 'error';
                //         $data['errors'][] = ['code' => 'summoner.' . $e->getCode(),'messange' => __('riot.summoner.' . $e->getCode())];
                //     }
                } else {
                    $data['status'] = 'error';
                    $data['errors'][] = ['code' => 'summoner.already','messange' => __('riot.summoner.already')];
                }
            break;

            //Установка данных с modalRegForm
            case 'modalRegForm':
                $leagues = config('games.lol.leagues');
                $validator = Validator::make(
                    $request->only('rang', 'division', 'roles'), [
                        'rang'      => ['required', Rule::in(array_keys($leagues))],
                        'division'  => [Rule::requiredIf(is_array($leagues[$request->input('rang')]))],
                ]);
                if ($validator->fails()) {
                    return response()->json($validator->errors(), 200);
                }

                $division = $leagues[$request->input('rang')]['division'];               
                $mmr = is_array($division) ? $division[$request->input('division')] : $division;  
               
                \Auth::user()->update([
                    'mmr' => $mmr,
                    'roles' => $request->input('roles')
                ]);
            break;

            case 'rolesEdit':    
                \Auth::user()->update([                   
                    'roles' => $request->input('roles')
                ]);
            break;
            case 'refreshAcc':    
                $profile  = $user->accounts()->where(['game' => 'lol', 'nickname'  => $request->input('nickname')])->first();
                $summoner = $riot->getSummoner($profile->profileId);                     
                $user->accounts()->where(['game' => 'lol', 'profileId' => $summoner->id])->first()->update([
                    'nickname'  => $summoner->name
                ]);

                $data['status']     = 'success';
                $data['nick']       = $summoner->name;
            break;
        }

        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
