<?php

namespace App\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminDeleteAllController extends Controller
{

    /**
     * Erases all information from the system except admin accounts.
     *
     * @return Illuminate\Http\Response
     */
    public function destroy()
    {

        // DB::table('failed_jobs')->truncate();
        // DB::table('jobs')->truncate();
        // DB::table('password_resets')->truncate();
        // DB::table('player_round')->truncate();
        DB::table('player_team')->truncate();
        // DB::table('players')->truncate();
        // DB::table('round_team')->truncate();
        // DB::table('rounds')->truncate();
        // DB::table('teams')->truncate();
        // DB::table('users')->where('role', 'coach')->delete();

    }

}
