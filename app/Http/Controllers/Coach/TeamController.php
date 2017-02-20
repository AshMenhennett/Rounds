<?php

namespace App\Http\Controllers\Coach;

use App\Team;
use Illuminate\Http\Request;

class TeamController extends \App\Http\Controllers\Controller
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

        if (! count($team->user) && ! count($user->team)) {
            // team nor user have association of other model.
            // let's save the 'association'
            $user->team()->save($team);
        }

        return redirect()->route('team.manage', [
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

        $user = $request->user();

        $user->team()->update([
            'user_id' => null,
        ]);

        return redirect()->route('teams.index');
    }

}
