<?php

namespace App\Transformers;

use App\Round;

class RoundTransformer extends \League\Fractal\TransformerAbstract
{

    /**
     * Transforms a Round into an array as below.
     *
     * @param  App\Round $round
     * @return array
     */
    public function transform(Round $round)
    {
        return [
            'id' => $round->id,
            'name' => $round->name,
            'default_date' => $round->default_date->toDateString(),
            'teams' => count($round->teams),
        ];
    }

}
