<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'role', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Attribute accessor for name property.
     *
     * @return string
     */
    public function getNameAttribute(){
        return $this->attributes['first_name'];
    }


    /**
     * A User has one Team.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function team()
    {
        return $this->hasOne(Team::class);
    }

    /**
     * Returns a User's first name.
     *
     * @return string
     */
    public function name()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Returns true if current user is an admin.
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Returns 0 if the User has no Team.
     *
     * @return boolean
     */
    public function associatedWithTeam()
    {
        return count($this->team);
    }

    /**
     * Whether a user is associated with a given Team.
     *
     * @param  App\Team    $team
     * @return boolean
     */
    public function hasTeam($team)
    {
        return $this->team()->get()->contains($team);
    }
}
