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
            'players' => count($team->players)
        ];
    }

}
