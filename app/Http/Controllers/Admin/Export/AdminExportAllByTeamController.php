<?php

namespace App\Http\Controllers\Admin\Export;

use Excel;
use App\Team;
use App\Round;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExportAllByTeamFormRequest;

class AdminExportAllByTeamController extends Controller
{

    /**
     * Exports all team data to an excel spreadsheet.
     *
     * @param  App\Http\Requests\Admin\ExportAllByTeamFormRequest $request
     * @return Illuminate\Http\Response
     */
    public function export(ExportAllByTeamFormRequest $request)
    {
        $team = Team::find($request->teamForTeamData);

        $now = Carbon::now()->toDateTimeString();

        Excel::create($team->name . ' Data -' . $now , function ($excel) use ($team) {
            $excel->sheet('Players', function ($sheet) use ($team) {
                $sheet->appendRow([
                    'Player Name', 'Temporary?'
                ]);

                $team->players->each(function ($player) use ($sheet) {
                    $sheet->appendRow([
                        $player->name, $player->temp ? 'yes' : 'no'
                    ]);
                });
            });

            $excel->sheet('Rounds', function ($sheet) use ($team) {
                $sheet->appendRow([
                    'Round #', 'Date'
                ]);

                $team->rounds->each(function ($round) use ($team, $sheet) {
                    $sheet->appendRow([
                        $round->name, $round->date($team)
                    ]);
                });
            });

            $team->rounds->each(function ($round) use ($team, $excel) {
                $excel->sheet('Round ' . $round->name, function ($sheet) use ($team, $round) {
                    $sheet->appendRow([
                        'Player Name', 'Best Player?', '2nd Best Player?', 'Quarters', 'Reason for Quarter Count'
                    ]);

                    \App\Round::find($round->id)->playersInTeam($team)->each(function ($player) use ($sheet) {
                        $sheet->appendRow([
                            $player->name,
                            $player->pivot->best_player ? 'yes' : 'no',
                            $player->pivot->second_best_player ? 'yes' : 'no',
                            $player->pivot->quarters,
                            $player->pivot->quarters_reason
                        ]);
                    });
                });
            });
        })->download('xls');

    }

}
