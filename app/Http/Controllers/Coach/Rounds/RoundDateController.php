<?php

namespace App\Http\Controllers\Coach\Rounds;

use App\Team;
use App\Round;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Coach\StoreRoundDateFormRequest;

class RoundDateController extends Controller
{

    /**
     * Sets the date for a Round and Team association.
     *
     * @param  App\Http\Requests\Coach\StoreRoundDateFormRequest $request
     * @param  App\Team                      $team
     * @param  App\Round                     $round
     * @return Illuminate\Http\Response
     */
    public function store(StoreRoundDateFormRequest $request, Team $team, Round $round)
    {
        $this->authorize('storeRoundDate', $team);

        $team->rounds()->updateExistingPivot($round->id, [
            'date' => $request->date
        ], true);

        $request->session()->flash('success_message', 'Round date updated!');

        return redirect()->route('coach.round.show', [
            'team' => $team,
            'round' => $round,
        ]);
    }

}
