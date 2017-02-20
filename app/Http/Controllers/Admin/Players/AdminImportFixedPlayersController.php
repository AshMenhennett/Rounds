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
<<<<<<< HEAD
        $players = collect($request->players);

        // transform each 'player' into an object
        $players = $players->map(function ($player) {
=======
        $player_data = collect($request->players);

        // transform each 'player' into an object
        $player_data = $player_data->map(function ($player) {
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
            return (object) $player;
        });

        // get all invalid entries
<<<<<<< HEAD
        $invalid_players = $players->filter(function ($player) {
=======
        $invalid_player_data = $player_data->filter(function ($player) {
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
            return ! Team::where('id', $player->team)->first() || $player->name == null || $player->name == '';
        })->values();

        // get all valid entries
<<<<<<< HEAD
        $valid_players = $players->filter(function ($player) {
=======
        $valid_player_data = $player_data->filter(function ($player) {
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
            return Team::where('id', $player->team)->first() && $player->name != null && $player->name != '';
        })->values();

        // save players
<<<<<<< HEAD
        $valid_players->each(function ($player) {
=======
        $valid_player_data->each(function ($player) {
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
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
<<<<<<< HEAD
            'invalid_player_data' => $invalid_players->all()
=======
            'invalid_player_data' => $invalid_player_data->all()
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
        ], 200);
    }

}
