<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{

    /**
     * Mass assignable fields.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'slug', 'name',
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
     * A Team has many rounds.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teams()
    {
        return $this->hasMany(Round::class);
    }

    /**
     * A Team belongs to many rounds.
     *
     * @return Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function rounds()
    {
        return $this->belongsToMany(Round::class);
    }

    /**
     * A Team belongs to many players.
     *
     * @return Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function players()
    {
        return $this->belongsToMany(Player::class);
    }

}
