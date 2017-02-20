<?php

namespace App\Http\Controllers\Admin\Players;

use Excel;
use App\Team;
use App\Player;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreImportedPlayersFormRequest;

class AdminImportPlayersController extends \App\Http\Controllers\Controller
{

    /**
     * Imports players from an excel spreadsheet.
     * Returns invalid players, if present.
     *
     * Teams are represented by their name in the submitted file.
     *
     * Utilized by Admin/Players/AdminImportPlayersComponent.vue
     *
     * @param  App\Http\Requests\Admin\StoreImportedPlayersFormRequest $request
     * @return Illuminate\Http\Response
     */
    public function store(StoreImportedPlayersFormRequest $request)
    {
        $now = Carbon::now()->toW3cString();
        $fileName = 'players-import-' . $now . '.xlsx';

        // moving imported file to local storage
        $request->file('players')->move(storage_path('/import/excel/players/'), $fileName);

        $players = null;
        try {
            // create a Collection from the results returned from file
            $players = collect(Excel::load(storage_path('import/excel/players/') . $fileName)->get());
        } catch(\ErrorException $e) {
            // no dates are expected, but handling the edge case
            abort(406, 'INVALID_DATE_PRESENT');
        }


        // reject all data that is not in correct format (there is not a 'player_name' key or the corresponding value is 'empty')
        $players = $players->reject(function ($player) {
            return (! isset($player['player_name']) && ! isset($player['team']))
                || ($player->player_name == null && $player->team == null)
                || ($player->player_name == '' && $player->team == '');
        })->values();

        if (! count($players)) {
            // file is considered empty (eg. wrong format)
            abort(400, 'FILE_EMPTY');
        }

        // clean up data structure
        $players = $players->map(function ($player) {
            $player['name'] = $player->player_name;
            unset($player['player_name']);
            return $player;
        });

        // get all invalid entries
        $invalid_player_data = $players->filter(function ($player) {
            return ! Team::where('name', $player->team)->first()
                || $player->name == null
                || $player->name == '';
        })->values();

        // get all valid entries
        $valid_player_data = $players->filter(function ($player) {
            return Team::where('name', $player->team)->first()
                && $player->name != null && $player->name != '';
        })->values();

        // create and attach players to a team
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
