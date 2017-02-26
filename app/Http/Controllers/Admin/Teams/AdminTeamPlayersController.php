<?php

namespace App\Http\Controllers\Admin\Teams;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminTeamPlayersController extends Controller
{

    /**
     * Returns all players for a given Team.
     *
     * Utilized by Admin/Export/AdminExportPlayerQuarterDataComponent.vue
     *
     * @param  App\Team   $team
     * @return
     */
    public function fetch(Team $team)
    {
        return response()->json($team->players, 200);
    }

}
