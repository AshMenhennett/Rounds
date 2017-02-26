<?php

namespace App;

use App\Team;
use Illuminate\Database\Eloquent\Model;

class Round extends Model
{

    /**
     * Date fields as Carbon instance
     */
    protected $dates = ['default_date'];

    /**
     * Mass assignable fields.
     *
     * Name is to be a numeric value, i.e. 1
     *
     * @var array
     */
    protected $fillable = [
        'name', 'default_date',
    ];

    /**
     * A Round belongs to many teams.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class)
                ->withPivot('date')
                ->withTimestamps();
    }

    /**
     * A Team belongs to many players.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function players()
    {
        return $this->belongsToMany(Player::class)
                ->withPivot('best_player', 'second_best_player', 'quarters', 'quarters_reason')
                ->withTimestamps();
    }

    /**
     * Returns date of a Round, given a Team.
     * If coach hasn't entered in a different date, returns 'default_date'
     *
     * @param  App\Team   $team
     * @return Illuminate\Http\Response
     */
    public function date(Team $team)
    {
        if ( isset($this->teams()->find($team->id)->pivot->date) ) {
            return $this->teams()->find($team->id)->pivot->date;
        }

        return $this->default_date;
    }

}
