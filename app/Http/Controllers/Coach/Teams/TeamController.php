<?php

namespace App\Http\Controllers\Coach\Teams;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeamController extends Controller
{

    /**
     * Saves a Team to a User.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function store(Request $request, Team $team)
    {
        $user = $request->user();

        if (! isset($team->user)) {
            // team doesn't belong to a user
            // let's save the 'association'
            $user->teams()->save($team);
        }

        return redirect()->route('coach.team.manage', [
            'team' => $team,
        ]);
    }

    /**
     * Removes a User (coach) from a Team.
     *
     * @param  Illuminate\Http\Request $request
     * @param  App\Team    $team
     * @return Illuminate\Http\Response
     */
    public function detach(Request $request, Team $team)
    {
        $this->authorize('detachUser', $team);

        $team->update([
            'user_id' => null,
        ]);

        return redirect()->route('coach.teams.index');
    }

}
