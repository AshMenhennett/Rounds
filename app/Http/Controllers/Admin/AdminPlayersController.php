<?php

namespace App\Http\Controllers\Admin;

use App\Player;
use Illuminate\Http\Request;
use App\Transformers\PlayerTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class AdminPlayersController extends \App\Http\Controllers\Controller
{

    /**
     * Displays Player listings for Admin.
     * Displays AdminPlayersComponent.vue Vue component.
     *
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.players.index');
    }

    /**
     * Displays all players as an array.
     * See App\Transformers\PlayerTransformer for data structure.
     *
     * @return array
     */
    public function fetch()
    {
        $players = Player::orderBy('name')->paginate(8);
        $playersCollection = $players->getCollection();

        return fractal()
                        ->collection($playersCollection)
                        ->transformWith(new PlayerTransformer)
                        ->paginateWith(new IlluminatePaginatorAdapter($players))
                        ->toArray();
    }

}
