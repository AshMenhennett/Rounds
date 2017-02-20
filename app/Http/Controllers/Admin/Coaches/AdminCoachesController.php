<?php

namespace App\Http\Controllers\Admin\Coaches;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\CoachTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class AdminCoachesController extends Controller
{

    /**
     * Displays all teams as an array for pagination.
     *
     * Utilized by Admin/Coaches/AdminDisplayCoachesComponent.vue
     *
     * See App\Transformers\CoachesTransformer for data structure.
     *
     * @return array
     */
    public function fetch()
    {
        $coaches = User::orderBy('first_name')->paginate(10);
        $coachesCollection = $coaches->getCollection();

        return fractal()
                        ->collection($coachesCollection)
                        ->transformWith(new CoachTransformer)
                        ->paginateWith(new IlluminatePaginatorAdapter($coaches))
                        ->toArray();
    }

}
