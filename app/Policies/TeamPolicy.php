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
     * Allows coach of Team and admin to see the Team management view.
     * Used in TeamManagementController
     *
     * @param  App\User   $user
     * @param  App\Team   $team
     * @return boolean
     */
    public function showManagement(User $user, Team $team)
    {
        return  $user->id === $team->user_id || $user->isAdmin();
    }

    /**
     * Allows a coach to detach themself from a Team.
     * Used in TeamController
     *
     * @param  App\User   $user
     * @param  App\Team   $team
     * @return boolean
     */
    public function detachUser(User $user, Team $team)
    {
        return  $user->id === $team->user_id;
    }

    /**
     * Below function are used for controllers other than TeamController and TeamManagementController.
     * Reason: We need to authorize, based on a Team. Hence, using this Policy.
     */

    /**
     * Allows coach of Team, as well as admin to view players of a Team.
     * Used in PlayerController
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
     * Used in PlayerController and RoundController
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
     * Used in PlayerController
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
     * Used in PlayerController
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
     * Used in PlayerController
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
     * Used in PlayerController
     *
     * @param  App\User   $user
     * @param  App\Team   $team
     * @return boolean
     */
    public function deletePlayer(User $user, Team $team)
    {
        return  $user->id === $team->user_id || $user->isAdmin();
    }

    /**
     * Allows coach and admin to view a Round and fill in data.
     * Used in RoundController
     *
     * @param  App\User   $user
     * @param  App\Team   $team
     * @return boolean
     */
    public function showRound(User $user, Team $team)
    {
        return  $user->id === $team->user_id || $user->isAdmin();
    }

    /**
     * Allows coach and admin to view available rounds.
     * Used in RoundsController
     *
     * @param  App\User   $user
     * @param  App\Team   $team
     * @return boolean
     */
    public function indexRounds(User $user, Team $team)
    {
        return  $user->id === $team->user_id || $user->isAdmin();
    }

    /**
     * Allows coach and admin to save a custom date for a Round.
     * Used in RoundDateController
     *
     * @param  App\User   $user
     * @param  App\Team   $team
     * @return boolean
     */
    public function storeRoundDate(User $user, Team $team)
    {
        return  $user->id === $team->user_id || $user->isAdmin();
    }

    /**
     * Allows coach and admin to save input for a Round.
     * Used in RoundController
     *
     * @param  App\User   $user
     * @param  App\Team   $team
     * @return boolean
     */
    public function storeRound(User $user, Team $team)
    {
        return  $user->id === $team->user_id || $user->isAdmin();
    }

}
