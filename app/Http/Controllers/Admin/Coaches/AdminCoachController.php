<?php

namespace App\Http\Controllers\Admin\Coaches;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminCoachController extends Controller
{

    /**
     * Deletes a Coach, if they have not played any rounds.
     *
     * Utilized by Admin/Coaches/AdminDisplayCoachesComponent.vue
     *
     * @param  App\User $coach
     * @return Illuminate\Http\Response
     */
    public function destroy(User $coach)
    {

        if ($coach->isAdmin()) {
            // user is admin, let's not let any admin remove another admin, not even their own account
            return response()->json(null, 403);
        }

        if (count($coach->teams)) {
            $coach->teams->each(function ($team) {
                // detach coach from teams
                $team->update([
                    'user_id' => null,
                ]);
            });
        }

        $coach->delete();

        return response()->json(null, 200);
    }

}
