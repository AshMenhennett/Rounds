<?php

namespace App\Http\Controllers\Admin\Export;

use Excel;
use App\Team;
use App\Round;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExportBestPlayersByTeamFormRequest;

class AdminExportBestPlayersByTeamController extends Controller
{

    /**
     * Exports best players data to an excel spreadsheet.
     *
     * Note: Second Best Players are refered to as 'Team Spirits' in this controller.
     *
     * @param  App\Http\Requests\Admin\ExportBestPlayersByTeamFormRequest $request
     * @return Illuminate\Http\Response
     */
    public function export(ExportBestPlayersByTeamFormRequest $request)
    {
        $team = Team::find($request->teamForBestPlayers);

        $now = Carbon::now()->toDateTimeString();

        $rounds = $team->rounds->map(function ($round) use ($team) {
            // add some properies to each round
            $round['date'] = $round->date($team);
            $round['team'] = $team;

            // add best and second best counts to players
            $team->players->map(function ($player) {
                $player['best_player_count'] = $player->countBestPlayerStatus();
                $player['team_spirit_count'] = $player->countSecondBestPlayerStatus();
                return $player;
            });

            $round['best_player'] = $team->players->filter(function ($player) use ($round) {
                return $player->rounds()->find($round->id) && $player->rounds()->find($round->id)->pivot->best_player == 1;
            })->first();

            $round['team_spirit'] = $team->players->filter(function ($player) use ($round) {
                return $player->rounds()->find($round->id) && $player->rounds()->find($round->id)->pivot->second_best_player == 1;
            })->first();

            return $round;
        });

        $bestPlayers = $rounds->map(function ($round) {
            return $round['best_player'];
        })->reject(function ($player) {
            return $player == null;
        })->unique()
            ->values()
            ->sortBy(function ($player) {
                return $player->countBestPlayerStatus();
            })
            ->reverse();

        $teamSpirits = $rounds->map(function ($round) {
            return $round['team_spirit'];
        })->reject(function ($player) {
            return $player == null;
        })->unique()
            ->values()
            ->sortBy(function ($player) {
                return $player->countSecondBestPlayerStatus();
            })
            ->reverse();

        Excel::create($team->name . ' Best Players -' . $now , function ($excel) use ($team, $rounds, $bestPlayers, $teamSpirits) {
            $excel->sheet('Best Players', function ($sheet) use ($team, $rounds, $bestPlayers, $teamSpirits) {

                $sheet->appendRow([
                    'Round #', 'Date', 'Team', 'Best Player', 'Team Spirit'
                ]);

                $rounds->each(function ($round) use ($team, $sheet) {
                    if ($round['best_player'] != null) {
                        // prevent null pointer exceptions
                        $round['best_player'] = $round['best_player']->name;
                    }
                    if ($round['team_spirit'] != null) {
                        $round['team_spirit'] = $round['team_spirit']->name;
                    }
                    $sheet->appendRow([
                        $round->name, $round->date->format('d/m/Y'), $team->name, $round->best_player, $round->team_spirit
                    ]);
                });

                $sheet->appendRow([
                    '', '', '', '', ''
                ]);

                $sheet->appendRow([
                    '', '', '', '', ''
                ]);

                $sheet->appendRow([
                    'BEST PLAYER VOTES'
                ]);

                $bestPlayers->each(function ($player) use ($sheet) {
                    $sheet->appendRow([
                        $player->name, $player->best_player_count . ' votes'
                    ]);
                });

                $sheet->appendRow([
                    '', '', '', '', ''
                ]);

                $sheet->appendRow([
                    'TEAM SPIRIT VOTES'
                ]);

                $teamSpirits->each(function ($player) use ($sheet) {
                    $sheet->appendRow([
                       $player->name, $player->team_spirit_count . ' votes'
                    ]);
                });

            });
        })->download('xls');

    }

}
