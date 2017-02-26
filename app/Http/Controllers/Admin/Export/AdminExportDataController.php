<?php

namespace App\Http\Controllers\Admin\Export;

use App\Team;
use App\Round;
use App\Player;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminExportDataController extends Controller
{

    /**
     * Displays Forms to export necessary data.
     *
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.export.index', [
            'teams' => Team::all(),
            'rounds' => Round::all(),
            'players' => Player::all(),
        ]);
    }

}
