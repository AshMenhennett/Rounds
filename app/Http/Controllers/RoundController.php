<?php

namespace App\Http\Controllers;

use App\Team;
use App\Round;
use Illuminate\Http\Request;

class RoundController extends Controller
{

    /**
     * Displays available rounds.
     *
     * @param  App\Team   $team
     * @param  App\Round  $round
     * @return Illuminate\Http\Response
     */
    public function index(Team $team, Round $round)
    {
        // need authorization for a team, so must use TeamPolicy
        $this->authorize('indexRound', $team);

        return view('team.round', [
            'team' => $team,
            'round' => $round,
        ]);
    }

    // if (! $team->rounds()->find($round->id)) {
        //     // checking that the round hasn't already been filled for the Team
        //     $team->rounds()->save($round, [
        //         'date' => \Carbon\Carbon::now(),
        //     ]);
        // }

}
