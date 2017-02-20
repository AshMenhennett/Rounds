<?php

namespace App\Http\Controllers\Coach\Rounds;

use Fractal;
use App\Team;
use App\Round;
use App\Player;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoundController extends Controller
{

    /**
     * Saves association between a Team and Round.
     *
     * @param  App\Team   $team
     * @param  App\Round  $round
     * @return Illuminate\Http\Response
     */
    public function store(Team $team, Round $round)
    {
        $this->authorize('storeRound', $team);

        if (! count($team->rounds()->where('round_id', $round->id)->get())) {
            // the team is not attached to the round, so let's attach them
            $team->rounds()->attach($round);
        }

        return redirect()->route('coach.round.show', [
            'team' => $team,
            'round' => $round,
        ]);
    }

    /**
     * Displays a view to fill in data for a Round including the date.
     *
     * Displays Coach/Rounds/RoundInputComponent.vue
     *
     * @param  App\Team   $team
     * @param  App\Round  $round
     * @return Illuminate\Http\Response
     */
    public function show(Team $team, Round $round)
    {
        $this->authorize('showRound', $team);

        return view('coach.round.show', [
            'team' => $team,
            'round' => $round
        ]);
    }

    /**
     * Returns players, how many rounds they have played in and data associated with a Round.
     *
     * Utilized by Coach/Rounds/RoundInputComponent.vue
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
            function ($player) use ($round) {
                return [
                    'id' => $player->id,
                    'name' => $player->name,
                    'temp' => $player->temp,
                    'recent' => $player->recent,
                    'rounds' => count($player->rounds),
                    'round' => [
                        // whether or not the player is already playing in this Round.
                        'exists' => count($player->rounds()->find($round->id)) ? 1 : 0,
                        // gross code, but will do for now
                        'best_player' => isset($player->rounds()->find($round->id)->pivot->best_player) ? $player->rounds()->find($round->id)->pivot->best_player : 0,
                        'second_best_player' => isset($player->rounds()->find($round->id)->pivot->second_best_player) ? $player->rounds()->find($round->id)->pivot->second_best_player : 0,
                        'quarters' => isset($player->rounds()->find($round->id)->pivot->quarters) ? $player->rounds()->find($round->id)->pivot->quarters : 0,
                    ]
                ];
            }
        )->toArray();
    }

    /**
     * Updates a Team's Round data.
     *
     * @param  Illuminate\Http\Request $request
     * @param  App\Team    $team
     * @param  App\Round   $round
     * @return Illuminate\Http\Response
     */
    public function update(Request $request, Team $team, Round $round)
    {
        $this->authorize('updateRound', $team);

        if (! isset($request->players) || ! count($request->players)) {
            // abort with 400 error, as no data has been submitted- bad request
            abort(400, 'NO_DATA_SUBMITTED');
        }

        // validation
        foreach ($request->players as $player_data) {

            // making sure all indexes are available in request
            if (! array_key_exists('round', $player_data) ||
                ! array_key_exists('quarters', $player_data['round']) ||
                ! array_key_exists('best_player', $player_data['round']) ||
                ! array_key_exists('second_best_player', $player_data['round']) ||
                ! array_key_exists('id', $player_data) ) {
                abort(400, 'INCOMPLETE_DATA');
            }

            if (((int) $player_data['round']['quarters'] > 4) || ((int) $player_data['round']['quarters'] < 0) || ! is_int($player_data['round']['quarters'])) {
                // invalid data for quarters submitted
                // return JSON with id of invalid quarter input
                return response()->json(['player_id' => $player_data['id']], 422);
            }

            // find Player or throw ModelNotFoundException
            $player = Player::findOrFail($player_data['id']);

            if (! $player->team->contains('id', $team->id)) {
                // exit early, form may have been 'hacked' and players that don't exist in this Team may have been used.
                abort(404, 'PLAYERS_NOT_FOUND_IN_THIS_TEAM');
            }
        }

        // get player ids that belong to this Round and Team as an array
        $player_ids = $round->players()->whereIn('player_id', $team->playersById())->get()->pluck('id')->all();

        if (count($player_ids)) {
            // remove all players that belong to the current Team and start fresh
            // not using sync(), as we don't want to remove associations from player_round from other teams.
            $round->players()->detach($player_ids);
        }

        // remove all recent flags on players in current Team
        $team->players->each(function ($player) {
            $player->update([
                'recent' => 0,
            ]);
        });

        // process input
        foreach ($request->players as $player_data) {

            // get the Player
            $player = Player::findOrFail($player_data['id']);

            // set Player as 'recent'
            $player->update([
                'recent' => 1,
            ]);

            // attach Player to Round and add pivot data
            $round->players()->attach($player, [
                'best_player' => $player_data['round']['best_player'] != 0 ? 1 : 0,
                'second_best_player' => $player_data['round']['second_best_player'] != 0 ? 1 : 0,
                'quarters' => $player_data['round']['quarters'],
            ]);
        }

        /**
         * HTTP Status codes used:
         * 404 - ModelNotFoundException - User 'hacks' form and enters invalid player ids
         * 422 - User enters invalid value for quarter field
         * 400 - User didn't post any data.
         * 200 - OK
         */

        return response()->json(null, 200);
    }

    /**
     * Removes the association between a Team, a Round and players from that team in that Round.
     *
     * @param  App\Team   $team
     * @param  App\Round  $round
     * @return Illuminate\Http\Response
     */
    public function destroy(Team $team, Round $round)
    {
        $this->authorize('destroyRoundForTeam', $team);

        $team->rounds()->detach($round);

        $round->players()->detach($team->players);

        $team->players(function ($player) {
            $round->players()->detach($player);
        });

        return redirect()->route('coach.rounds.index', [
            'team' => $team,
        ]);
    }

}
