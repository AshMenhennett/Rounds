<?php

namespace App\Transformers;

use App\User;

class CoachTransformer extends \League\Fractal\TransformerAbstract
{

    /**
     * Transforms a User (coach) into an array as below.
     *
     * @param  App\User $coach
     * @return array
     */
    public function transform(User $coach)
    {
        return [
            'id' => $coach->id,
            'first_name' => $coach->first_name,
            'last_name' => $coach->last_name,
            'email' => $coach->email,
            'role' => $coach->role,
            'team' => [
                'name' => $coach->team ? $coach->team->first()->name : null,
                'slug' => $coach->team ? $coach->team->first()->slug : null,
            ]
        ];
    }

}
