<?php

namespace App\Http\Controllers\Admin;

use App\Team;
use Illuminate\Http\Request;

class AdminTeamsController extends \App\Http\Controllers\Controller
{

    /**
     * Returns an array of all teams.
     * Utilized by AdminTeamSelectComponent.vue Vue component.
     *
     * @return array
     */
    public function fetch()
    {
        return Team::all()->toArray();
    }

}
