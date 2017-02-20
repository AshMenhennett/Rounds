<?php

namespace App\Http\Controllers\Admin\Teams;

use Excel;
use App\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreImportedTeamsFormRequest;

class AdminImportTeamsController extends Controller
{

    /**
     * Imports teams from an excel spreadsheet.
     * Returns invalid and valid teams in spreadsheet, to be used to determine whether the import operation was successful or not and anything inbetween.
     * See Admin/Teams/AdminImportTeamsComponent.vue for conditionals and resulting output.
     *
     * Utilized by Admin/Teams/AdminImportTeamsComponent.vue
     *
     * @param  App\Http\Requests\Admin\StoreImportedTeamsFormRequest $request
     * @return Illuminate\Http\Response
     */
    public function store(StoreImportedTeamsFormRequest $request)
    {
        $now = Carbon::now()->toW3cString();
        $fileName = 'teams-import-' . $now . '.xlsx';

        // moving uploaded file to application storage
        $request->file('teams')->move(storage_path('/import/excel/teams/'), $fileName);

<<<<<<< HEAD
        $teams = null;
        try {
            // create a Collection from the results returned from file
            $teams = collect(Excel::load(storage_path('import/excel/teams/') . $fileName)->get());
        } catch(\ErrorException $e) {
            // no dates are expected, but handling the edge case
            abort(406, 'INVALID_DATE_PRESENT');
        }

        // reject all data that is not in correct format (there is not a 'team_name' key or the corresponding value is 'empty')
        $teams = $teams->reject(function ($team) {
            return ! isset($team['team_name'])
                || $team->team_name == null
                || $team->team_name == '';
=======
        // create a Collection from the results returned from file
        $teams = collect(Excel::load(storage_path('import/excel/teams/') . $fileName)->get());

        // reject all data that is not in correct format (there is not a 'team_name' key or the corresponding value is 'empty')
        $teams = $teams->reject(function ($team) {
            return ! isset($team['team_name']) || $team->team_name == null || $team->team_name == '';
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
        })->values();

        if (! count($teams)) {
            // file is considered empty (eg. wrong format)
            abort(400, 'FILE_EMPTY');
        }

        // clean up Collection
        $teams = $teams->map(function ($team) {
            $team['name'] = $team->team_name;
            unset($team['team_name']);
            return $team;
        });

        // get all invalid teams (the team name or slug is already taken)
        $invalid_teams = $teams->filter(function ($team) {
            return Team::where('name', $team->name)->first()
                || Team::where('name', strtolower($team->name))->first()
                || Team::where('slug', str_slug($team->name, '-'))->first();
        })->values();

        // get all valid teams that are not duplicates
        $valid_teams = $teams->filter(function ($team) {
            return ! Team::where('name', $team->name)->first()
                && ! Team::where('name', strtolower($team->name))->first()
                && ! Team::where('slug', str_slug($team->name, '-'))->first();
        })->unique()->values();

        // create valid teams
        $valid_teams->each(function ($team) {
            Team::create(['name' => $team->name]);
        });

        return response()->json([
                'invalid_team_data' => $invalid_teams,
                'valid_team_data' => $valid_teams,
            ], 200);
    }

}
