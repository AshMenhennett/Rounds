<?php

namespace App\Http\Controllers\Coach;

use App\Team;
use App\Player;
use Illuminate\Http\Request;
use App\Http\Requests\EditPlayerFormRequest;
use App\Http\Requests\CreatePlayerFormRequest;

class PlayerController extends \App\Http\Controllers\Controller
{

    /**
     * Creates a Player.
     * Utilized by TeamPlayersComponent.vue Vue component.
     *
     * @param  App\Http\Requests\CreatePlayerFormRequest $request
     * @param  App\Team    $team
     * @return Illuminate\Http\Response
     */
    public function store(CreatePlayerFormRequest $request, Team $team)
    {
        $this->authorize('storePlayer', $team);

        $player = $team->players()->create([
            'name' => $request->name,
            'temp' => $request->temp ? 1 : 0,
        ]);

        return response()->json($player, 200);
    }

    /**
     * Displays edit view for Player.
     *
     * @param  App\Team   $team
     * @param  App\Player $player
     * @return Illuminate\Http\Response
     */
    public function edit(Team $team, Player $player)
    {
        $this->authorize('editPlayer', $team);

        return view('player.edit', [
            'team' => $team,
            'player' => $player,
        ]);
    }

    /**
     * Updates a player's name and temp boolean.
     *
     * @param  App\Http\Requests\EditPlayerFormRequest $request
     * @param  App\Team    $team
     * @param  App\Player  $player
     * @return Illuminate\Http\Request
     */
    public function update(EditPlayerFormRequest $request, Team $team, Player $player)
    {
        $this->authorize('updatePlayer', $team);

        $player->update([
            'name' => $request->name,
            'temp' => $request->temp ? 1 : 0,
        ]);

        return redirect()->route('players.index', [
            'team' => $team,
        ]);
    }

    /**
     * Deletes a Player, if they have not played any rounds.
     * Utilized by TeamPlayersComponent.vue Vue component.
     *
     * @param  App\Team   $team
     * @param  App\Player $player
     * @return Illuminate\Http\Response
     */
    public function destroy(Team $team, Player $player)
    {
        $this->authorize('destroyPlayer', $team);

        if (count($player->rounds)) {
            // Player has played a round
            return response()->json(null, 400);
        }

        $player->delete();

        return response()->json(null, 200);
    }

}
