<?php

namespace App\Http\Controllers\Admin\Teams;

use App\Team;
use Illuminate\Http\Request;
use App\Transformers\TeamTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class AdminTeamsController extends \App\Http\Controllers\Controller
{

    /**
     * Returns an array of all teams.
     *
     * Utilized by Admin/Players/AdminCreatePlayerComponent.vue and Admin/Players/AdminImportPlayersComponent.vue
     *
     * @return array
     */
    public function fetch()
    {
        return Team::all()->toArray();
    }

    /**
     * Displays all teams as an array for pagination.
     *
     * Utilized by Admin/Teams/AdminDisplayTeamsComponent.vue
     *
     * See App\Transformers\TeamTransformer for data structure.
     *
     * @return array
     */
    public function fetchForPagination()
    {
        $teams = Team::orderBy('name')->paginate(10);
        $teamsCollection = $teams->getCollection();

        return fractal()
                        ->collection($teamsCollection)
                        ->transformWith(new TeamTransformer)
                        ->paginateWith(new IlluminatePaginatorAdapter($teams))
                        ->toArray();
    }

}
