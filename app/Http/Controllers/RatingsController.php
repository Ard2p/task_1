<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StatisticPlayers;
use App\Models\User;
use App\Models\Tournament;

class RatingsController extends Controller
{
  public function index()
  {
    // $user = User::find(82);
    // $user->mmr = 1800;
    // $user->save();
    // $tour = Tournament::find(5);
    // dd($tour);
    $statistics = StatisticPlayers::with('account')->with('user')
      ->whereMonth('created_at', date('m', strtotime('+3 hour')))
      ->whereYear('created_at', date('Y', strtotime('+3 hour')))
      ->where('game', 'lol')->orderBy('win', 'desc')->get()
      // ->limit(100)
      ->sortByDesc(
        function ($item) {
          $gameCount = $item->win + $item->lose;
          $K   = $item->k ? number_format($item->k / $gameCount, 1) : 0;
          $D   = $item->d ? number_format($item->d / $gameCount, 1) : 0;
          $A   = $item->a ? number_format($item->a / $gameCount, 1) : 0;
          $KA  = $K + $A;
          
          if ($KA == 0) $KDA = 0;
          else if ($D == 0) $KDA = $KA + 10;
          else $KDA = number_format($KA / $D, 2);
          return $KDA;
        }
      )->groupBy('win')->sortKeysDesc(); 

    return view('page.ratings.index', compact('statistics'));
  }
}
