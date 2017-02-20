<?php

namespace App\Http\Controllers\Coach\Teams;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeamManagementController extends Controller
{

    /**
     * Displays add players, leave team and fill in round buttons and Team stats.
     *
     * @param  Illuminate\Http\Request $request
     * @param  App\Team   $team
     * @return Illuminate\Http\Response
     */
    public function show(Request $request, Team $team)
    {
        $this->authorize('showManagement', $team);

        if (! $team->user()->count()) {
            if (! $request->user()->isAdmin()) {
                $request->session()->flash('warning_message', 'You need to join that Team before managing it!');
                return redirect()->route('coach.teams.index');
            }
            $request->session()->flash('warning_message', 'The Team needs a coach first!');
            return back();
        }

        return view('coach.team.manage', [
            'team' => $team,
        ]);
    }

}