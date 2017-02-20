<?php

namespace App\Http\Controllers\Admin\Teams;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreTeamFormRequest;
use App\Http\Requests\Admin\UpdateTeamFormRequest;

class AdminTeamController extends \App\Http\Controllers\Controller
{

    /**
     * Creates a Team.
     *
     * Utilized by Admin/Teams/AdminCreateTeamComponent.vue
     *
     * Team slug field is automatically generated in App\Providers\AppServiceProvider
     *
     * @param  App\Http\Requests\Admin\StoreTeamFormRequest $request
     * @return Illuminate\Http\Response
     */
    public function store(StoreTeamFormRequest $request)
    {
        $team = Team::create([
            'name' => $request->name,
        ]);

        return response()->json($team, 200);
    }

    /**
     * Displays edit form for a Team.
     *
     * Utilized by Admin/Teams/AdminDisplayTeamsComponent.vue
     *
     * @param  App\Team $team
     * @return Illuminate\Http\Response
     */
    public function edit(Team $team)
    {
        return view('admin.team.edit', [
            'team' => $team,
        ]);
    }

    /**
     * Updates a Team's name and slug.
     *
     * Admin can manually change the slug field of a Team, if they wish.
     *
     * @param  App\Http\Requests\Admin\UpdateTeamFormRequest $request
     * @param  App\Team  $team
     * @return Illuminate\Http\Request
     */
    public function update(UpdateTeamFormRequest $request, Team $team)
    {
        $team->update([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        $request->session()->flash('success_message', 'Team Updated!');

        return redirect()->route('admin.home', [
            'v' => $request->query()['v'],
        ]);
    }

    /**
<<<<<<< HEAD
     * Deletes a Team, if they do not have any players.
     *
=======
     * Deletes a Team, if they have not played any rounds.
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
     * Utilized by Admin/Teams/AdminDisplayTeamsComponent.vue
     *
     * @param  App\Team $team
     * @return Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        if (count($team->players)) {
<<<<<<< HEAD
            // Team has players
=======
            // Team has played in a round
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
            return response()->json(null, 400);
        }

        $team->delete();

        return response()->json(null, 200);
    }

}
