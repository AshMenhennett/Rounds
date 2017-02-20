<?php

namespace App\Http\Controllers\Admin\Rounds;

use Excel;
use App\Round;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreImportedRoundsFormRequest;

class AdminImportRoundsController extends Controller
{
    /**
     * Imports rounds from an excel spreadsheet.
     * Returns invalid rounds, if present.
     *
     * Rounds are represented by their name and date in the submitted file.
     *
     * Utilized by Admin/Rounds/AdminImportRoundsComponent.vue
     *
     * @param  App\Http\Requests\Admin\StoreImportedRoundsFormRequest $request
     * @return Illuminate\Http\Response
     */
    public function store(StoreImportedRoundsFormRequest $request)
    {
        $now = Carbon::now()->toW3cString();
        $fileName = 'rounds-import-' . $now . '.xlsx';

        // moving imported file to local storage
        $request->file('rounds')->move(storage_path('/import/excel/rounds/'), $fileName);

        $rounds = null;
        try {
            // create a Collection from the results returned from file
            $rounds = collect(Excel::load(storage_path('import/excel/rounds/') . $fileName)->get());
        } catch(\ErrorException $e) {
            abort(406, 'INVALID_DATE_PRESENT');
        }

        // reject all data that is not in correct format (there is not a 'name' and 'date' key or the corresponding value is 'empty')
        $rounds = $rounds->reject(function ($round) {
            return (! isset($round['round']) && ! isset($round['date']))
                || ($round->round == null && $round->date == null)
                || ($round->round == '' && $round->date == '');
        })->values();

        // clean up structure
        $rounds = $rounds->map(function($round) {
            $round['name'] = $round->round;
            unset($round['round']);
            // $round->date = (! date_create($round->date) || $round->date == '') ? null : $round->date; // set $round->date to null if it is not a valid date
            return $round;
        });

        if (! count($rounds)) {
            // file is considered empty (eg. wrong format)
            abort(400, 'FILE_EMPTY');
        }

        // get all invalid entries
        $invalid_rounds = $rounds->filter(function ($round) {
            return $round->name == null
                || $round->name == ''
                || $round->date == null
                || $round->date == '';
        })->values();

        // get all valid entries and create the rounds
        $rounds->filter(function ($round) {
            return $round->name != null
                && $round->name != ''
                && $round->date != null
                && $round->date != '';
        })->values()->each(function ($round) {
            Round::create([
                'name' => $round->name,
                'default_date' => $round->date,
            ]);
        });

        // if there is some invalid data, return it and let the user correct it.
        // subsequent 'corrections' will be handled by AdminImportFixedRoundsController
        return response()->json([
            'invalid_round_data' => $invalid_rounds->all()
        ], 200);
    }
}
