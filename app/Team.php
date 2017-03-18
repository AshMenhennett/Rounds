<?php

namespace App;

use App\Round;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{

    /**
     * Mass assignable fields.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'slug', 'name', 'best_players_allowed',
    ];

    /**
     * A Team is identified by its slug.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * A Team belongs to a User.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A Team belongs to many rounds.
     *
     * @return Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function rounds()
    {
        return $this->belongsToMany(Round::class)
                ->withPivot('date')
                ->withTimestamps();
    }

    /**
     * A Team belongs to many players.
     *
     * @return Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function players()
    {
        return $this->belongsToMany(Player::class)
                    ->withTimestamps();
    }

    /**
     * Return an array of Player ids associated with a Team.
     *
     * @return array
     */
    public function playersById()
    {
        return $this->players->pluck('id')->all();
    }

    /**
     * Return the Team's Coach or null, if no Coach exists for this Team.
     *
     * @return App\User | null
     */
    public function hasCoach()
    {
        return $this->user;
    }

    /**
     * Returns whether a Team is required to enter in best players when filling in a Round.
     *
     * @return boolean
     */
    public function bestPlayersAllowed()
    {
        return $this->best_players_allowed === 1;
    }

}
