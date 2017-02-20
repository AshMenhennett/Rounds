<?php

namespace App\Http\Controllers\Admin\Rounds;

use App\Round;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoundFormRequest;
use App\Http\Requests\Admin\UpdateRoundFormRequest;

class AdminRoundController extends Controller
{

    /**
     * Creates a Round.
     *
     * Utilized by Admin/Rounds/AdminCreateRoundComponent.vue
     *
     *
     * @param  App\Http\Requests\Admin\StoreRoundFormRequest $request
     * @return Illuminate\Http\Response
     */
    public function store(StoreRoundFormRequest $request)
    {
        \Log::info($request->date);
        $round = Round::create([
            'name' => $request->name,
        ]);

        if ($request->date != null || $request->date != "") {
            $round->update([
                'default_date' => \Carbon\Carbon::parse($request->date),
            ]);
        }

        return response()->json($round, 200);
    }

    /**
     * Displays edit form for a Round.
     *
     * Utilized by Admin/Rounds/AdminDisplayRoundsComponent.vue
     *
     * @param  App\Round $team
     * @return Illuminate\Http\Response
     */
    public function edit(Round $round)
    {
        return view('admin.round.edit', [
            'round' => $round,
        ]);
    }

    /**
     * Updates a Round's name and default date.
     *
     * @param  App\Http\Requests\Admin\UpdateRoundFormRequest $request
     * @param  App\Round  $round
     * @return Illuminate\Http\Request
     */
    public function update(UpdateRoundFormRequest $request, Round $round)
    {
        $round->update([
            'name' => $request->name,
        ]);

        if ($request->date != null || $request->date != "") {
            $round->update([
                'default_date' => $request->date,
            ]);
        }

        $request->session()->flash('success_message', 'Round Updated!');

        return redirect()->route('admin.home', [
            'v' => $request->query()['v'],
        ]);
    }

    /**
     * Deletes a Round, if it does not have any teams.
     *
     * Utilized by Admin/Rounds/AdminDisplayRoundsComponent.vue
     *
     * @param  App\Round $round
     * @return Illuminate\Http\Response
     */
    public function destroy(Round $round)
    {
        if (count($round->teams)) {
            // Round has teams
            return response()->json(null, 400);
        }

        $round->delete();

        return response()->json(null, 200);
    }

}
