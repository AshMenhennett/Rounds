<?php

namespace App\Http\Controllers;

use Fractal;
use App\Team;
use App\Player;
use Illuminate\Http\Request;
use App\Http\Requests\EditPlayerFormRequest;
use App\Http\Requests\CreatePlayerFormRequest;

class PlayerController extends Controller
{

    /**
     * Displays TeamPlayersComponent.vue.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Resposne
     */
    public function index(Request $request, Team $team)
    {
        $this->authorize('indexPlayers', $team);

        return view('players.index', [
            'team' => $team,
        ]);
    }

    /**
     * Returns players and how many rounds they have played in.
     * Utilized by TeamPlayersComponent.vue Vue component.
     * I.e. {"data":[{"player":{"id":0, ..., "rounds":1}, ..., ...]}
     *
     * @param  Illuminate\Http\Request $request
     * @param  App\Team    $team
     * @return array
     */
    public function fetch(Request $request, Team $team)
    {
        $this->authorize('fetchPlayers', $team);

        return Fractal::collection($team->players()->orderBy('name')->get())->transformWith(
            function ($player) {
                return [
                    'id' => $player->id,
                    'name' => $player->name,
                    'temp' => $player->temp,
                    'recent' => $player->recent,
                    'rounds' => count($player->rounds)
                ];
            }
        )->toArray();
    }

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
            'temp' => $request->temp,
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
     * Updates a player's name.
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
    public function delete(Team $team, Player $player)
    {
        $this->authorize('deletePlayer', $team);

        if (count($player->rounds)) {
            // Player has played a round
            return response()->json(null, 400);
        }

        $player->delete();

        return response()->json(null, 200);
    }

}
