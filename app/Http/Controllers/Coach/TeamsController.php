<?php

namespace App\Http\Controllers\Coach;

use App\Team;
use Illuminate\Http\Request;

class TeamsController extends \App\Http\Controllers\Controller
{

    /**
     * Display all teams that do not have a User associated.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $teams = Team::where('user_id', null)->get();

        $user = $request->user();

        if (count($user->team)) {
            // user already has a team.. redirect to home- shows links to manage their team
            return redirect()->route('home');
        }

        return view('teams.index', [
            'teams' => $teams,
        ]);
    }

}
