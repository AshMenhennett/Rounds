<?php

namespace App\Http\Controllers\Admin\Players;

use App\Player;
use Illuminate\Http\Request;
use App\Transformers\PlayerTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class AdminPlayersController extends \App\Http\Controllers\Controller
{

    /**
     * Displays all players as an array for pagination.
     *
     * Utilized by Admin/Players/AdminDisplayPlayersComponent.vue
     *
     * See App\Transformers\PlayerTransformer for data structure.
     *
     * @return array
     */
    public function fetch()
    {
        $players = Player::orderBy('name')->paginate(10);
        $playersCollection = $players->getCollection();

        return fractal()
                        ->collection($playersCollection)
                        ->transformWith(new PlayerTransformer)
                        ->paginateWith(new IlluminatePaginatorAdapter($players))
                        ->toArray();
    }

}
