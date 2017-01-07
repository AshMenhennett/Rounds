<?php

namespace App\Http\Controllers;

use App\Team;
use App\Round;
use Illuminate\Http\Request;

class TeamController extends Controller
{

    /**
     * Displays all rounds for a Team to select.
     *
     * @param  App\Team   $team
     * @return Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        $this->authorize('show', $team);

        $rounds = Round::all();

        return view('team.show', [
            'team' => $team,
            'rounds' => $rounds,
        ]);
    }

    /**
     * Associates a Team with a User.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function store(Request $request, Team $team)
    {
        $user = $request->user();

        if (! count($team->user) && ! count($user->team)) {
            // team nor user have association of other model.
            // let's save the association
            $user->team()->save($team);
        }

        return redirect()->route('team.show', [
            'team' => $team,
        ]);
    }

    /**
     * Detaches a User from a Team.
     *
     * @param  Illuminate\Http\Request $request
     * @param  App\Team    $team
     * @return Illuminate\Http\Response
     */
    public function detach(Request $request, Team $team)
    {
        $this->authorize('detach', $team);

        $user = $request->user();

        $user->team()->update([
            'user_id' => null,
        ]);

        return redirect()->route('teams.index');
    }

}
