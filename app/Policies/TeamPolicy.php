<?php

namespace App\Policies;

use App\User;
use App\Team;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Admin can only show a Team and index a Round.
     */

    public function show(User $user, Team $team)
    {
        return  $user->id === $team->user_id || $user->isAdmin();
    }

    public function detach(User $user, Team $team)
    {
        return  $user->id === $team->user_id;
    }

    /**
     * Below function are used for controllers other than TeamController.
     * Reason: We need to authorize, based on a Team. Hence, using this Policy.
     */

    /**
     * Allows coach of Team, as well as admin to view available rounds, under a Team.
     *
     * @param  App\User   $user
     * @param  App\Team   $team
     * @return boolean
     */
    public function indexRound(User $user, Team $team)
    {
        return  $user->id === $team->user_id || $user->isAdmin();
    }

    /**
     * Allows coach of Team, as well as admin to view players of a Team.
     *
     * @param  App\User   $user
     * @param  App\Team   $team
     * @return boolean
     */
    public function indexPlayers(User $user, Team $team)
    {
        return  $user->id === $team->user_id || $user->isAdmin();
    }

    /**
     * Allows coach of Team, as well as admin to view players of a Team as an array.
     *
     * @param  App\User   $user
     * @param  App\Team   $team
     * @return boolean
     */
    public function fetchPlayers(User $user, Team $team)
    {
        return  $user->id === $team->user_id || $user->isAdmin();
    }

    /**
     * Allows coach of Team, as well as admin to create a new Player.
     *
     * @param  App\User   $user
     * @param  App\Team   $team
     * @return boolean
     */
    public function storePlayer(User $user, Team $team)
    {
        return  $user->id === $team->user_id || $user->isAdmin();
    }

    /**
     * Allows coach of Team, as well as admin to edit a Player.
     *
     * @param  App\User   $user
     * @param  App\Team   $team
     * @return boolean
     */
    public function editPlayer(User $user, Team $team)
    {
        return  $user->id === $team->user_id || $user->isAdmin();
    }

    /**
     * Allows coach of Team, as well as admin to update a Player.
     *
     * @param  App\User   $user
     * @param  App\Team   $team
     * @return boolean
     */
    public function updatePlayer(User $user, Team $team)
    {
        return  $user->id === $team->user_id || $user->isAdmin();
    }

    /**
     * Allows coach of Team, as well as admin to delete a Player.
     *
     * @param  App\User   $user
     * @param  App\Team   $team
     * @return boolean
     */
    public function deletePlayer(User $user, Team $team)
    {
        return  $user->id === $team->user_id || $user->isAdmin();
    }
}
