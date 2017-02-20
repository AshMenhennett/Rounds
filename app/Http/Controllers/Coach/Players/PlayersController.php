<?php

namespace App\Http\Controllers\Coach\Players;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\PlayerTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class PlayersController extends Controller
{

    /**
     * Displays list of current players for a team with input to add more.
     *
     * Displays Coach/Players/TeamPlayersComponent.vue
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Resposne
     */
    public function index(Request $request, Team $team)
    {
        $this->authorize('indexPlayers', $team);

        // we need to pass the Team through to the component and we will utilize the fetch method on this controller to gather all players for that Team.
        return view('coach.players.index', [
            'team' => $team,
        ]);
    }

    /**
     * Displays all players as an array for pagination.
     *
     * Utilized by Coach/Players/TeamPlayersComponent.vue
     *
     * See App\Transformers\PlayerTransformer for data structure.
     *
     * @param  Illuminate\Http\Request $request
     * @param  App\Team    $team
     * @return array
     */
    public function fetch(Request $request, Team $team)
    {
        $this->authorize('fetchPlayers', $team);

        $players = $team->players()->orderBy('name')->paginate(10);
        $playersCollection = $players->getCollection();

        return fractal()
                ->collection($playersCollection)
                ->transformWith(new PlayerTransformer)
                ->paginateWith(new IlluminatePaginatorAdapter($players))
                ->toArray();
    }

}
