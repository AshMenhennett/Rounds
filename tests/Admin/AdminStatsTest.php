<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminStatsTest extends BrowserKitTestCase
{

    /** @test */
    public function admin_can_get_system_stats()
    {
        $admin = factory(App\User::class)->create(['role' => 'admin']);
        $coaches = factory(App\User::class, 5)->create(['role' => 'coach']);
        $teams = factory(App\Team::class, 5)->create();
        $players = factory(App\Player::class, 5)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $teams->each(function ($team) use ($coaches, $rounds) {
            $coaches->each(function ($coach) use ($team) {
                $coach->teams()->save($team);
            });

            $rounds->each(function ($round) use ($team) {
                $round->teams()->attach($team);
            });
        });

        $this->actingAs($admin)
            ->call('GET', '/admin/stats/fetch');

        $teams->each(function ($team) {
            $this->see('"teams":[{"id":' . $team->id . ',"user_id":' . $team->user_id . ',"name":"' . $team->name . '","slug":"' . $team->slug . '","created_at":"' . $team->created_at .'","updated_at":"' . $team->updated_at . '","best_players_allowed":' . $team->best_players_allowed ? 1 : 0);
        });

        $players->each(function ($player) {
            $this->see('{"id":' . $player->id . ',"name":"' . $player->name . '","temp":0,"recent":0,"created_at":"'. $player->created_at . '","updated_at":"' . $player->updated_at . '"}');
        });

        $rounds->each(function ($round) {
            $this->see('{"id":' . $round->id . ',"name":"' . $round->name . '","default_date":"'. $round->default_date .'","created_at":"'. $round->created_at . '","updated_at":"' . $round->updated_at . '"}');
        });

        $this->see('"filledInRoundsCount":25');

        $coaches->each(function ($coach) {
            $this->see('{"id":' . $coach->id . ',"first_name":"' . $coach->first_name . '","last_name":"' . $coach->last_name . '","email":"'. $coach->email .'","role":"coach","created_at":"' . $coach->created_at . '","updated_at":"' . $coach->updated_at . '"}');
        });

        // admin is also considered a coach- quick check to make sure admin is in array as well
        $this->see('{"id":' . $admin->id . ',"first_name":"' . $admin->first_name . '","last_name":"' . $admin->last_name . '","email":"'. $admin->email .'","role":"admin","created_at":"' . $admin->created_at . '","updated_at":"' . $admin->updated_at . '"}');

        $teams->each(function ($team) {
            if ($team->hasCoach()) {
                // Note: admin is not assigned a team in this test, so we do not expect to see admin in the above assertion
                $this->see('{"id":' . $team->id . ',"user_id":' . $team->user_id . ',"name":"' . $team->name . '","slug":"' . $team->slug . '","created_at":"' . $team->created_at .'","updated_at":"' . $team->updated_at . '","best_players_allowed":' . $team->best_players_allowed ? 1 : 0);
            }
        });
    }

}
