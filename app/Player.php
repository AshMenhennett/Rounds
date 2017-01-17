<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{

    /**
     * Mass assignable fields.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'temp', 'recent',
    ];

    /**
     * A Player belongs to many teams.
     * Only reason for having BelongsToMany, rather than BelongsTo, is simply to utilize the pivot table- player_team.
     * As per the business logic of this application, we will only allow 1 Player to belong to 1 Team.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function team()
    {
        return $this->belongsToMany(Team::class)
                    ->withTimestamps();
    }

    /**
     * A Player belongs to many rounds.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rounds()
    {
        return $this->belongsToMany(Round::class)
                ->withPivot('best_player', 'second_best_player', 'quarters')
                ->withTimestamps();
    }

}
