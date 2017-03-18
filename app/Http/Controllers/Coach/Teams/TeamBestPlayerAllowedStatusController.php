<?php

namespace App\Http\Controllers\Coach\Teams;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeamBestPlayerAllowedStatusController extends Controller
{

    /**
     * Returns a Teams bestPlayersAllowed() status.
     *
     * Utilized by Coach/Rounds/RoundInputComponent.vue
     *
     * @return boolean
     */
    public function index(Team $team)
    {
        $this->authorize('toggleTeamBestPlayerAllowed', $team);

        return response()->json($team->bestPlayersAllowed(), 200);
    }

}
