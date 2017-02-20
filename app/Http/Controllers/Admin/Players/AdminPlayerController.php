<?php

namespace App\Http\Controllers\Admin\Players;

use App\Team;
use App\Player;
use Illuminate\Http\Request;
use App\Http\Requests\Generic\UpdatePlayerFormRequest;
use App\Http\Requests\Admin\StorePlayerWithTeamFormRequest;

class AdminPlayerController extends \App\Http\Controllers\Controller
{

    /**
     * Creates a Player.
     *
     * Utilized by Admin/Players/AdminCreatePlayerComponent.vue
     *
     * @param  App\Http\Requests\Generic\CreatePlayerWithTeamFormRequest $request
     * @return Illuminate\Http\Response
     */
    public function store(StorePlayerWithTeamFormRequest $request)
    {
        $team = Team::find($request->team);

        $player = $team->players()->create([
            'name' => $request->name,
            'temp' => $request->temp ? 1 : 0,
        ]);

        return response()->json($player, 200);
    }

    /**
     * Displays edit form for a Player.
     *
     * Utilized by Admin/Players/AdminDisplayPlayersComponent.vue
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
     * @param  App\Http\Requests\Generic\UpdatePlayerFormRequest $request
     * @param  App\Player  $player
     * @return Illuminate\Http\Request
     */
    public function update(UpdatePlayerFormRequest $request, Player $player)
    {
        $player->update([
            'name' => $request->name,
            'temp' => $request->temp ? 1 : 0,
        ]);

        $request->session()->flash('success_message', 'Player Updated!');

        return redirect()->route('admin.home', [
            'v' => $request->query()['v'],
        ]);
    }

    /**
     * Deletes a Player, if they have not played any rounds.
     *
     * Utilized by Admin/Players/AdminDisplayPlayersComponent.vue
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
