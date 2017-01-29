<?php

namespace App\Http\Controllers\Admin;

use Excel;
use App\Team;
use App\Player;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\StoreImportPlayersFormRequest;

class AdminImportPlayersController extends \App\Http\Controllers\Controller
{

    /**
     * Imports players from an excel spreadsheet.
     * Returns invalid players, if present.
     *
     * Teams are represented by their name in the submitted file.
     *
     * @param  App\HttpRequests\StoreImportPlayersFormRequest $request
     * @return Illuminate\Http\Response
     */
    public function store(StoreImportPlayersFormRequest $request)
    {
        $now = Carbon::now()->toW3cString();
        $fileName = 'players-import-' . $now . '.xls';

        // moving imported file to local storage
        $request->file('players')->move(storage_path('/import/excel/'), $fileName);

        // creating a Collection from the results returned from file
        $player_data = collect(Excel::load(storage_path('import/excel/') . $fileName)->get());

        // clean up data structure
        $player_data = $player_data->map(function ($player) {
            $player['name'] = $player->player_name;
            unset($player['player_name']);
            return $player;
        });

        // get all invalid entries
        $invalid_player_data = $player_data->filter(function ($player) {
            return ! Team::where('name', $player->team)->first() || $player->name == null;
        })->values();

        // get all valid entries
        $valid_player_data = $player_data->filter(function ($player) {
            return Team::where('name', $player->team)->first() && $player->name !== null;
        })->values();

        // save players
        $valid_player_data->each(function ($player) {
            $team = Team::where('name', $player->team)->first();
            $team->players()->attach(
                Player::create([
                    'name' => $player->name,
                    'temp' => 0,
                ])
            );
        });

        // if there is some invalid data, return it and let the user correct it.
        // subsequent 'corrections' will be handled by AdminImportFixedPlayersController
        return response()->json([
            'invalid_player_data' => $invalid_player_data->all()
        ], 200);
    }

}
