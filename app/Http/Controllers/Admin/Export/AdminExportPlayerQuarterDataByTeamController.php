<?php

namespace App\Http\Controllers\Admin\Export;

use Excel;
use App\Team;
use App\Player;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExportPlayerQuarterDataByTeamFormRequest;

class AdminExportPlayerQuarterDataByTeamController extends Controller
{

    /**
     * Export quarter data for a Player.
     *
     * @param   App\Http\Requests\Admin\ExportPlayerQuarterDataByTeamFormRequest $request
     * @return Illuminate\Http\Response
     */
    public function export(ExportPlayerQuarterDataByTeamFormRequest $request)
    {
        $team = Team::where('slug', $request->team)->first();

        $now = Carbon::now()->toDateTimeString();

        if (! $team->players()->find($request->player)) {
            abort(400, 'PLAYER_DOES_NOT_EXIST');
        }

        $player = Player::find($request->player);

        Excel::create($player->name . ' - ' . $team->name . ' Quarter Data -' . $now, function ($excel) use ($team, $player) {
            $excel->sheet('Quarter Data', function ($sheet) use ($team, $player) {
                $sheet->appendRow([
                    'Team', 'Round #', 'Date', 'Player', 'Quarters', 'Reason'
                ]);

                $player->rounds->each(function ($round) use ($sheet, $player, $team) {
                    $sheet->appendRow([
                        $team->name, $round->name, $round->date($team), $player->name, $round->pivot->quarters, $round->pivot->quarters_reason
                    ]);
                });
            });
        })->download('xls');

    }

}
