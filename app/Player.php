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
        'team_id', 'round_id', 'name', 'temp', 'recent',
    ];

    /**
     * A Player belongs to a Team.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * A Player belongs to many rounds.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rounds()
    {
        return $this->belongsToMany(Round::class);
    }

}
