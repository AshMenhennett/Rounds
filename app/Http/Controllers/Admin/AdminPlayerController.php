<?php

namespace App\Http\Controllers\Admin;

use App\Team;
use App\Player;
use Illuminate\Http\Request;
use App\Http\Requests\EditPlayerFormRequest;
use App\Http\Requests\CreatePlayerWithTeamFormRequest;

class AdminPlayerController extends \App\Http\Controllers\Controller
{

    /**
     * Creates a Player.
     * Utilized by AdminCreatePlayerComponent.vue Vue component.
     *
     * @param  App\Http\Requests\CreatePlayerWithTeamFormRequest $request
     * @return Illuminate\Http\Response
     */
    public function store(CreatePlayerWithTeamFormRequest $request)
    {
        $team = Team::find($request->team);

        $player = $team->players()->create([
            'name' => $request->name,
            'temp' => $request->temp ? 1 : 0,
        ]);

        return response()->json($player, 200);
    }

    /**
     * Displays edit view for Player.
     * Utilized by PlayerComponent.vue Vue component.
     *
     * @param  App\Player $player
     * @return Illuminate\Http\Response
     */
    public function edit(Player $player)
    {
        return view('admin.player.edit', [
            'player' => $player,
        ]);
    }

    /**
     * Updates a player's name and temp boolean.
     *
     * @param  App\Http\Requests\EditPlayerFormRequest $request
     * @param  App\Player  $player
     * @return Illuminate\Http\Request
     */
    public function update(EditPlayerFormRequest $request, Player $player)
    {
        $player->update([
            'name' => $request->name,
            'temp' => $request->temp ? 1 : 0,
        ]);

        return redirect()->route('admin.home');
    }

    /**
     * Deletes a Player, if they have not played any rounds.
     * Utilized by AdminPlayersComponent.vue Vue component.
     *
     * @param  App\Player $player
     * @return Illuminate\Http\Response
     */
    public function destroy(Player $player)
    {
        if (count($player->rounds)) {
            // Player has played a round
            return response()->json(null, 400);
        }

        $player->delete();

        return response()->json(null, 200);
    }

}
