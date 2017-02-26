<?php

namespace App\Http\Controllers\Admin\Export;

use Excel;
use App\Team;
use App\Player;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExportTeamQuarterDataFormRequest;

class AdminExportTeamQuarterDataController extends Controller
{

    /**
     * Export quarter data for all players in a Team.
     *
     * @param   App\Http\Requests\Admin\ExportTeamQuarterDataFormRequest $request
     * @return Illuminate\Http\Response
     */
    public function export(ExportTeamQuarterDataFormRequest $request)
    {
        $team = Team::find($request->teamForQuarterData);

        $now = Carbon::now()->toDateTimeString();

        Excel::create($team->name . ' Quarter Data -' . $now, function ($excel) use ($team) {
            $excel->sheet('Quarter Data', function ($sheet) use ($team) {
                $sheet->appendRow([
                    'Team', 'Round #', 'Date', 'Player', 'Quarters', 'Reason'
                ]);

                $team->rounds->each(function ($round) use ($sheet, $team) {
                    $round->players->filter(function ($player) use ($team) {
                        return $team->players()->find($player->id);
                    })->each(function ($player) use ($sheet, $team, $round) {
                        $sheet->appendRow([
                            $team->name, $round->name, $round->date($team), $player->name, $player->pivot->quarters, $player->pivot->quarters_reason
                        ]);
                    });
                });
            });
        })->download('xls');

    }

}
