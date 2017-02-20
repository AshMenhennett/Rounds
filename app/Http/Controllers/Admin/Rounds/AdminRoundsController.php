<?php

namespace App\Http\Controllers\Admin\Rounds;

use App\Round;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\RoundTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class AdminRoundsController extends Controller
{

    /**
     * Displays all rounds as an array for pagination.
     *
     * Utilized by Admin/Rounds/AdminDisplayRoundsComponent.vue
     *
     * See App\Transformers\RoundTransformer for data structure.
     *
     * @return array
     */
    public function fetch()
    {
        $rounds = Round::orderBy('default_date')->paginate(10);
        $roundsCollection = $rounds->getCollection();

        return fractal()
                        ->collection($roundsCollection)
                        ->transformWith(new RoundTransformer)
                        ->paginateWith(new IlluminatePaginatorAdapter($rounds))
                        ->toArray();
    }

}
