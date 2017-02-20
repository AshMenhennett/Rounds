<?php

namespace App\Http\Controllers\Coach;

use App\Team;
use App\Round;
use Illuminate\Http\Request;

class RoundsController extends \App\Http\Controllers\Controller
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

        return view('rounds.index', [
            'team' => $team,
            'rounds' => $rounds,
        ]);
    }

}
