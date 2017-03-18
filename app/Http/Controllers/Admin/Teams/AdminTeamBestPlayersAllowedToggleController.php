<?php

namespace App\Http\Controllers\Admin\Teams;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminTeamBestPlayersAllowedToggleController extends Controller
{
    /**
     * Toggles the need for a Coach to fill in the Best Players section of the Coach/RoundInputComponent.vue
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
        if ($team->bestPlayersAllowed()) {
            $team->update(['best_players_allowed' => 0]);
        } else {
            $team->update(['best_players_allowed' => 1]);
        }

        return redirect()->route('coach.team.manage', [
            'team' => $team,
        ]);
    }

}
