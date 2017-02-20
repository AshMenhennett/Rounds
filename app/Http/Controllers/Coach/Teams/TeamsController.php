<?php

namespace App\Http\Controllers\Coach\Teams;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeamsController extends Controller
{

    /**
     * Display all paginated teams that do not have a User associated.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $teams = Team::where('user_id', null)->orderBy('name')->paginate(5);

        if (count($request->user()->team)) {
            // user already has a team.. redirect to home- shows links to manage their team
            return redirect()->route('home');
        }

        return view('coach.teams.index', [
            'teams' => $teams,
        ]);
    }

}
