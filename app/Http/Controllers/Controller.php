<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

use RiotAPI\LeagueAPI\LeagueAPI;
use Debugbar;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function __construct (){        

    }
    function riotAPI(){
        return new LeagueAPI(config('games.lol.api_config'));
    }
}




// class Controller{

//     private $container;

//     function __construct($container){
//         $this->container = $container; 

//         $settings_res = Settings::all()->toArray(); 
//         foreach($settings_res as $value){
//             $settings[$value['name']] = $value['value'];
//         }
//         $this->DOM->set('site', $settings);  

//         if($this->session->get('user_id')){
//             $user = Users::where('id',  $this->session->user_id)->First()->toArray();        
//             $this->DOM->set('user', $user);   
//         }   
//     }

//     function __get($name){
//         if($this->container->{$name})
//             return $this->container->{$name};
//     }
// }