<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CoachPlayerTest extends BrowserKitTestCase
{

    /** @test */
    public function user_can_access_players_as_array_in_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $players = factory(App\Player::class, 5)->create();

        $user->team()->save($team);

        $players->each(function ($player) use (&$team) {
            $team->players()->attach($player);
        });

        $this->actingAs($user)
            ->visit('/teams/' . $team->slug . '/players/fetch');

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function player_data_as_array_in_team_is_correct()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $players = factory(App\Player::class, 5)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $user->team()->save($team);

        $players->each(function ($player) use (&$team) {
            $team->players()->attach($player);
        });

        $players->each(function ($player) use (&$rounds) {
            $rounds->each(function ($round) use (&$player) {
                $player->rounds()->attach($round);
            });
        });

        $this->actingAs($user)
            ->visit('/teams/' . $team->slug . '/players/fetch');

        $players->each(function ($player) {
            $this->see('{"id":' . $player->id . ',"name":"'. $player->name .'","temp":0,"recent":0,"rounds":5}');
        });

    }

    /** @test */
    public function user_can_add_player_in_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/players/new', [
                'name' => 'Bob',
                'temp' => 1,
            ]);

        $this->assertResponseStatus(200);

        $this->seeInDatabase('players', [
            'name' => 'Bob',
            'temp' => 1,
        ]);
    }

    /** @test */
    public function player_gets_0_for_temp_value_if_its_not_submitted_when_creating_new_player_in_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/players/new', [
                'name' => 'Bob'
            ]);

        $this->seeInDatabase('players', [
            'name' => 'Bob',
            'temp' => 0,
        ]);
    }

    /** @test */
    public function user_can_edit_and_update_player_name_and_temp_in_UI_in_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $player = factory(App\Player::class)->create();

        $user->team()->save($team);
        $team->players()->attach($player);

        $this->actingAs($user)
            ->visit('/teams/' . $team->slug . '/players/' . $player->id . '/edit')
            ->see($player->name);

        $this->type('Alice Menhennett', 'name')
            ->check('temp')
            ->press('Update');

        $this->seePageIs('/teams/' . $team->slug . '/players');

        $this->seeInDatabase('players', [
            'id' => $player->id,
            'name' => 'Alice Menhennett',
            'temp' => 1
        ]);

        // editing the player again
        $this->visit('/teams/' . $team->slug . '/players/' . $player->id . '/edit')
            ->see('Alice Menhennett');

        // unchecking temp
        $this->uncheck('temp')
            ->press('Update');

        $this->seeInDatabase('players', [
            'id' => $player->id,
            'name' => 'Alice Menhennett',
            'temp' => 0
        ]);
    }

    /** @test */
    public function user_can_delete_player_that_hasnt_played_in_any_rounds_in_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $player = factory(App\Player::class)->create();

        $user->team()->save($team);
        $team->players()->attach($player);

        $this->actingAs($user)
            ->call('DELETE', '/teams/' . $team->slug . '/players/' . $player->id);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function user_cant_delete_player_that_has_played_in_any_rounds_in_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $player = factory(App\Player::class)->create();
        $round = factory(App\Round::class)->create();

        // testing all model associations
        $user->team()->save($team);
        $team->players()->attach($player);
        $player->rounds()->attach($round);
        $round->teams()->attach($team);

        $this->actingAs($user)
            ->call('DELETE', '/teams/' . $team->slug . '/players/' . $player->id);

        $this->assertResponseStatus(400);
    }

    /** @test */
    public function user_can_see_how_many_rounds_that_a_player_has_played_in_team_via_db_and_relation()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $player = factory(App\Player::class)->create(['name' => 'Bob']);
        $rounds = factory(App\Round::class, 3)->create();

        $user->team()->save($team);
        $team->players()->attach($player);

        $rounds->each(function ($round) use (&$player) {
            $player->rounds()->attach($round);
        });
        $rounds->each(function ($round) use (&$team) {
            $round->teams()->attach($team);
        });

        // we would just count() the entries in player_round to get number of rounds played
        $rounds->each(function ($round) use (&$player) {
            $this->seeInDatabase('player_round', [
                'player_id' => $player->id,
                'round_id' => $round->id
            ]);
        });

        // count() elements in collection returned from Player and Round relationship
        $this->assertTrue(count($player->rounds) === 3);
    }

    /**
     * Validation
     */

    /** @test */
    public function user_gets_redirected_when_creating_player_with_no_name_in_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/players/new', [
                //
            ]);

        // redirect due to failed validation
        $this->assertResponseStatus(302);
    }

    /** @test */
    public function user_gets_redirected_when_updating_player_with_no_name_in_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $player = factory(App\Player::class)->create();

        $user->team()->save($team);
        $team->players()->attach($player);

        $this->actingAs($user)
            ->call('PUT', '/teams/' . $team->slug . '/players/' . $player->id, [
                //
            ]);

        // redirect due to failed validation
        $this->assertResponseStatus(302);
    }

    /**
     * Authorization
     */

    /** @test */
    public function user_can_access_their_own_teams_players_index()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $players = factory(App\Player::class, 2)->create();

        $user->team()->save($team);
        $players->each(function($player) use (&$team) {
            $team->players()->attach($player);
        });

        $this->actingAs($user)
            ->visit('/teams/' . $team->slug . '/players');

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function other_user_cannot_access_someone_elses_team_players_index()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();
        $players = factory(App\Player::class, 2)->create();

        $users->first()->team()->save($team);
        $players->each(function($player) use (&$team) {
            $team->players()->attach($player);
        });

        $this->actingAs($users->last())
            ->call('GET', '/teams/' . $team->slug . '/players');

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function admin_user_is_able_to_access_another_users_team_players_index()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();
        $players = factory(App\Player::class, 2)->create();

        $players->each(function($player) use (&$team) {
            $team->players()->attach($player);
        });

        $this->actingAs($user)
            ->visit('/teams/' . $team->slug . '/players');

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function other_user_cannot_add_player_to_other_users_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/players/new', [
                'name' => 'Bob',
                'temp' => 1,
            ]);

        $this->assertResponseStatus(403);

        $this->dontSeeInDatabase('players', [
            'name' => 'Bob',
            'temp' => 1,
        ]);
    }

    /** @test */
    public function admin_user_can_add_player_to_other_users_team()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/players/new', [
                'name' => 'Bob',
                'temp' => 1,
            ]);

        $this->assertResponseStatus(200);

        $this->seeInDatabase('players', [
            'name' => 'Bob',
            'temp' => 1,
        ]);
    }

    /** @test */
    public function user_can_update_player_name_and_temp_in_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $player = factory(App\Player::class)->create(['name' => 'Milly', 'temp' => 1]);

        $user->team()->save($team);
        $team->players()->attach($player);

        $this->actingAs($user)
            ->call('PUT', '/teams/' . $team->slug . '/players/' . $player->id, [
                'name' => 'Alice Menhennett',
                'temp' => 0,
            ]);

        // redirected
        $this->assertResponseStatus(302);

        $this->seeInDatabase('players', [
            'id' => $player->id,
            'name' => 'Alice Menhennett',
            'temp' => 0
        ]);
    }

    /** @test */
    public function other_user_cannot_update_player_name_and_temp_for_other_users_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $player = factory(App\Player::class)->create();

        $team->players()->attach($player);

        $this->actingAs($user)
            ->call('PUT', '/teams/' . $team->slug . '/players/' . $player->id, [
            'name' => 'Bob Bobby',
            'temp' => 1,
        ]);

        $this->assertResponseStatus(403);

        $this->dontSeeInDatabase('players', [
            'id' => $player->id,
            'name' => 'Bob Bobby',
            'temp' => 1
        ]);
    }

    /** @test */
    public function admin_user_can_update_player_name_and_temp_for_other_users_team()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();
        $player = factory(App\Player::class)->create(['name' => 'Marley', 'temp' => 0]);

        $team->players()->attach($player);

        $this->actingAs($user)
            ->call('PUT', '/teams/' . $team->slug . '/players/' . $player->id, [
                'name' => 'Bob Bobby',
                'temp' => 1,
            ]);

        // checking redirection
        $this->assertResponseStatus(302);

        $this->seeInDatabase('players', [
            'id' => $player->id,
            'name' => 'Bob Bobby',
            'temp' => 1
        ]);
    }

    /** @test */
    public function other_user_cannot_delete_player_that_hasnt_played_in_any_rounds_in_other_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $player = factory(App\Player::class)->create();

        $team->players()->attach($player);

        $this->actingAs($user)
            ->call('DELETE', '/teams/' . $team->slug . '/players/' . $player->id);

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function admin_user_can_delete_player_that_hasnt_played_in_any_rounds_in_other_team()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();
        $player = factory(App\Player::class)->create();

        $team->players()->attach($player);

        $this->actingAs($user)
            ->call('DELETE', '/teams/' . $team->slug . '/players/' . $player->id);

        $this->assertResponseStatus(200);
    }

}
