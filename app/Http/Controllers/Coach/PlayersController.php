<?php

namespace App\Http\Controllers\Coach;

use Fractal;
use App\Team;
use Illuminate\Http\Request;

class PlayersController extends \App\Http\Controllers\Controller
{

    /**
     * Displays list of current players with input to add more.
     *
     * Displays Coach/TeamPlayersComponent.vue
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
     *
     * Utilized by Coach/TeamPlayersComponent.vue
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

}
