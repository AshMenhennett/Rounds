<?php

namespace App\Http\Controllers\Admin\Players;

use App\Team;
use App\Player;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreImportedFixedPlayersFormRequest;

class AdminImportFixedPlayersController extends \App\Http\Controllers\Controller
{

    /**
     * Stores fixed player data, after re-submission due to invalid data.
     * Returns invalid players, if still present.
     *
     * Teams are represented by their id.
     *
     * Utilized by Admin/Players/AdminImportPlayersComponent.vue
     *
     * @param  App\Http\Requests\Admin\StoreImportedFixedPlayersFormRequest $request
     * @return Illuminate\Http\Response
     */
    public function store(StoreImportedFixedPlayersFormRequest $request)
    {
        $players = collect($request->players);

        // transform each 'player' into an object
        $players = $players->map(function ($player) {
            return (object) $player;
        });

        // get all invalid entries
        $invalid_players = $players->filter(function ($player) {
            return ! Team::where('id', $player->team)->first() || $player->name == null || $player->name == '';
        })->values();

        // get all valid entries
        $valid_players = $players->filter(function ($player) {
            return Team::where('id', $player->team)->first() && $player->name != null && $player->name != '';
        })->values();

        // save players
        $valid_players->each(function ($player) {
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
            'invalid_player_data' => $invalid_players->all()
        ], 200);
    }

}
