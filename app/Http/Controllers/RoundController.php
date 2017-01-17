<?php

namespace App\Http\Controllers;

use Fractal;
use App\Team;
use App\Round;
use App\Player;
use Illuminate\Http\Request;


class RoundController extends Controller
{

    /**
     * Displays a view to fill in data for a Round inc date.
     * Displays RoundInputComponent.vue Vue component.
     *
     * @param  App\Team   $team
     * @param  App\Round  $round
     * @return Illuminate\Http\Response
     */
    public function show(Team $team, Round $round)
    {
        $this->authorize('showRound', $team);

        if (! count($team->rounds()->where('round_id', $round->id)->get())) {
            // the team is not attached to the round, so let's attach them
            $team->rounds()->attach($round);
        }

        return view('round.show', [
            'team' => $team,
            'round' => $round
        ]);
    }

    /**
     * Returns players, how many rounds they have played in and data associated with a Round.
     * Utilized by RoundInputComponent.vue Vue component.
     * I.e. {"data":[{"player":{"id":0, ..., "rounds":1, "round":{"exists": 1, best_player":1, "second_best_player":0, "quarters":2}}, ..., ...]}
     *
     * @param  Illuminate\Http\Request $request
     * @param  App\Team    $team
     * @param  App\Round    $team
     * @return array
     */
    public function fetchPlayers(Request $request, Team $team, Round $round)
    {
        $this->authorize('fetchPlayers', $team);

        return Fractal::collection($team->players()->orderBy('name')->get())->transformWith(
            function ($player) use (&$round) {
                return [
                    'id' => $player->id,
                    'name' => $player->name,
                    'temp' => $player->temp,
                    'recent' => $player->recent,
                    'rounds' => count($player->rounds),
                    'round' => [
                        // whether or not the player is already playing in this Round.
                        'exists' => count($player->rounds()->find($round->id)) ? 1 : 0,
                        'best_player' => isset($player->rounds()->find($round->id)->pivot->best_player) ? $player->rounds()->find($round->id)->pivot->best_player : 0,
                        'second_best_player' => isset($player->rounds()->find($round->id)->pivot->second_best_player) ? $player->rounds()->find($round->id)->pivot->second_best_player : 0,
                        'quarters' => isset($player->rounds()->find($round->id)->pivot->quarters) ? $player->rounds()->find($round->id)->pivot->quarters : 0,
                    ]
                ];
            }
        )->toArray();
    }

    public function store(Request $request, Team $team, Round $round)
    {
        $this->authorize('storeRound', $team);

        // get player ids that belong to this Round and Team as an array
        $player_ids = $round->players()->whereIn('player_id', $team->playersById())->get()->pluck('id')->all();

        if (count($player_ids)) {
            // remove all players that belong to the current Team and to this Round and start fresh
            // not using sync(), as we don't want to remove associations from player_round from other teams.
            $round->players()->detach($player_ids);
        }

        // process input
        foreach ($request->players as $player_data) {

            if ((int) $player_data['round']['quarters'] > 4 || ! is_int($player_data['round']['quarters'])) {
                // invalid data for quarters submitted
                // return JSON with id of invalid quarter input
                return response()->json(['player_id' => $player_data['id']], 422);
            }

            // find Player or throw ModelNotFoundException
            $player = Player::findOrFail($player_data['id']);

            // set Player as 'recent'
            $player->update([
                'recent' => 1,
            ]);

            // attach Player to Round and add pivot data
            $round->players()->attach($player, [
                'best_player' => $player_data['round']['best_player'],
                'second_best_player' => $player_data['round']['second_best_player'],
                'quarters' => $player_data['round']['quarters'],
            ]);
        }

        return response()->json(null, 200);
    }

}
