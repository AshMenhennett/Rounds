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
        DB::table('failed_jobs')->delete();
        DB::table('jobs')->delete();
        DB::table('password_resets')->delete();
        DB::table('player_round')->delete();
        DB::table('player_team')->delete();
        DB::table('round_team')->delete();
        DB::table('players')->delete();
        DB::table('rounds')->delete();
        DB::table('teams')->delete();
        DB::table('users')->where('role', 'coach')->delete();

        return response()->json(null, 200);
    }

}
