<?php

namespace App\Http\Controllers\Coach\Teams;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeamsController extends Controller
{

    /**
     * Display all paginated teams.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $teams = Team::where('user_id', '!=', $request->user()->id)->orWhereNull('user_id')->orderBy('name')->paginate(5);

        return view('coach.teams.index', [
            'teams' => $teams,
        ]);
    }

}
