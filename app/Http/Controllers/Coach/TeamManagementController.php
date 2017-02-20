<?php

namespace App\Http\Controllers\Coach;

use App\Team;
use Illuminate\Http\Request;

class TeamManagementController extends \App\Http\Controllers\Controller
{

    /**
     * Displays add players, leave team and fill in round buttons and Team stats.
     *
     * @param  App\Team   $team
     * @return Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        $this->authorize('showManagement', $team);

        return view('team.manage', [
            'team' => $team,
        ]);
    }

}
