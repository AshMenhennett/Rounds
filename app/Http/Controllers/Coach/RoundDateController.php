<?php

namespace App\Http\Controllers\Coach;

use App\Team;
use App\Round;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRoundDateFormRequest;

class RoundDateController extends \App\Http\Controllers\Controller
{

    /**
     * Sets the date for a Round and Team relation.
     *
     * @param  App\Http\Requests\StoreRoundDateFormRequest $request
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

        return redirect()->route('round.show', [
            'team' => $team,
            'round' => $round,
        ]);
    }

}
