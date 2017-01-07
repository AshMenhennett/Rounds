<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Round extends Model
{

    /**
     * Mass assignable fields.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * A Round belongs to many teams.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    /**
     * A Team has many players.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function players()
    {
        return $this->hasMany(Player::class);
    }

}
