<?php

namespace App\Http\Controllers\Coach\Rounds;

use App\Team;
use App\Round;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoundsController extends Controller
{

    /**
     * Displays all rounds for a Team to select and fill in.
     *
     * @param  App\Team   $team
     * @return Illuminate\Http\Response
     */
    public function index(Team $team)
    {
        $this->authorize('indexRounds', $team);

        $rounds = Round::all();

        return view('coach.team.rounds.index', [
            'team' => $team,
            'rounds' => $rounds,
        ]);
    }

}
