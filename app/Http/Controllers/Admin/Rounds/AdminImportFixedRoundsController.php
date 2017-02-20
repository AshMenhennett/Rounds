<?php

namespace App\Http\Controllers\Admin\Rounds;

use App\Round;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreImportedFixedRoundsFormRequest;

class AdminImportFixedRoundsController extends Controller
{

    /**
     * Stores fixed round data, after re-submission due to invalid data.
     * Returns invalid rounds, if still present.
     *
     * Utilized by Admin/Rounds/AdminImportRoundsComponent.vue
     *
     * @param  App\Http\Requests\Admin\StoreImportedFixedRoundsFormRequest $request
     * @return Illuminate\Http\Response
     */
    public function store(StoreImportedFixedRoundsFormRequest $request)
    {
        $rounds = collect($request->rounds);

        // transform each round into an object
        $rounds = $rounds->map(function ($round) {
            $round = (object) $round;
            $round->date = (! date_create($round->date) || $round->date == '') ? null : $round->date; // set $round->date to null if it is not a valid date
            return $round;
        });

        // get all invalid entries
        $invalid_rounds = $rounds->filter(function ($round) {
            return $round->name == null
                || $round->name == ''
                || $round->date == null
                || $round->date == '';
        })->values();

        // get all valid entries and create the rounds
        $valid_rounds = $rounds->filter(function ($round) {
            return $round->name != null
                && $round->name != ''
                && $round->date != null
                && $round->date != '';
        })->values()->each(function ($round) {
            Round::create([
                'name' => $round->name,
                'default_date' => Carbon::parse($round->date),
            ]);
        });

        // if there is still some invalid data, return it and let the user correct it.
        // subsequent 'corrections' will be handled by this method.
        return response()->json([
            'invalid_round_data' => $invalid_rounds->all()
        ], 200);
    }

}
