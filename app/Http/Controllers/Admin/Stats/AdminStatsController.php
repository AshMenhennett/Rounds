<?php

namespace App\Http\Controllers\Admin\Stats;

use App\User;
use App\Team;
use App\Round;
use App\Player;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminStatsController extends Controller
{

    /**
     * Returns all required statistics about Teams, Rounds, Coaches and Players
     *
     * Utilized by Admin/AdminStatsComponent.vue
     *
     * @return array
     */
    public function fetch()
    {
        return response()->json([
                'teams' => Team::all(),
                'players' => Player::all(),
                'rounds' => Round::all(),
                'filledinRoundsCount' => \DB::table('round_team')->count(),
                'coaches' => User::all(),
                'teamsWithCoaches' => Team::where('user_id', '!=', null)->get(),
            ], 200);
    }

}
