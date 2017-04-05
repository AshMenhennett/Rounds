<?php

namespace App\Http\Controllers\Admin\Teams;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminTeamKickCoachController extends Controller
{

    /**
     * Kicks a coach off of their Team.
     * @param  Illuminate\Http\Request $request
     * @param  App\Team $team
     * @return Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
        if ($team->hasCoach() && ! $team->belongsToCoach($request->user())) {
            // the team has a coach, but it is not the current admin user
            $team->update([
                'user_id' => null,
            ]);
        }

        return redirect()->route('coach.team.manage', [
            'team' => $team,
        ]);
    }

}
