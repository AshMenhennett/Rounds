<?php

namespace App\Http\Controllers\Admin;

use App\Team;
use App\Player;
use Illuminate\Http\Request;
use App\Http\Requests\StoreImportFixedPlayersFormRequest;

class AdminImportFixedPlayersController extends \App\Http\Controllers\Controller
{

    /**
     * Stores fixed player data, after re-submission due to invalid data.
     * Returns invalid players, if still present.
     * Teams are represented by their id.
     *
     * @param  App\Http\Requests\StoreImportFixedPlayersFormRequest $request
     * @return Illuminate\Http\Response
     */
    public function store(StoreImportFixedPlayersFormRequest $request)
    {
        $player_data = collect($request->players);

        // turn each 'player' into a collection and create properties that are accessible via object->method notation
        $player_data = $player_data->map(function ($player) {
            $player = collect($player);
            $player->name = $player['name'];
            $player->team = $player['team'];
            return $player;
        });

        // get all invalid entries
        $invalid_player_data = $player_data->filter(function ($player) {
            return ! Team::where('id', $player->team)->first() || $player->name == null;
        })->values();

        // get all valid entries
        $valid_player_data = $player_data->filter(function ($player) {
            return Team::where('id', $player->team)->first() && $player->name !== null;
        })->values();

        // save players
        $valid_player_data->each(function ($player) {
            $team = Team::where('id', $player->team)->first();
            $team->players()->attach(
                Player::create([
                    'name' => $player->name,
                    'temp' => 0,
                ])
            );
        });

        // if there is still some invalid data, return it and let the user correct it.
        // subsequent 'corrections' will be handled by this method.
        return response()->json([
            'invalid_player_data' => $invalid_player_data->all()
        ], 200);
    }

}
