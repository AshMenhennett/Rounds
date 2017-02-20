<?php

namespace App\Transformers;

use App\Team;

class TeamTransformer extends \League\Fractal\TransformerAbstract
{

    /**
     * Transforms a Team into an array as below.
     *
     * @param  App\Team $team
     * @return array
     */
    public function transform(Team $team)
    {
        return [
            'id' => $team->id,
            'name' => $team->name,
            'slug' => $team->slug,
<<<<<<< HEAD
            'hasCoach' => $team->user_id ? 1 : 0,
=======
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
            'players' => count($team->players)
        ];
    }

}
