<?php

namespace App\Http\Controllers;

use App\Team;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * Displays home view.
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return view('home', [
            'team' => $user->team,
        ]);
    }

}
