<?php

namespace App\Policies;

use App\User;
use App\Round;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoundPolicy
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

}
