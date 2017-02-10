<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminPlayerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function admin_can_fetch_paginated_players()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();
        $players = factory(App\Player::class, 5)->create();

        $players->each(function ($player) use (&$team) {
            $team->players()->attach($player);
        });

        $this->actingAs($user)
            ->call('GET', '/admin/players/fetch');

        $players->each(function ($player) {
            $this->see('{"id":' . $player->id . ',"name":"' . $player->name . '","temp":0,"rounds":0}');
        });

        $this->see('"meta":{"pagination":{"total":5,"count":5,"per_page":10,"current_page":1,"total_pages":1,"links":[]}}');
    }

    /** @test */
    public function admin_can_create_new_player()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();

        $this->actingAs($user)
            ->call('POST', '/admin/players/new', [
                'name' => 'Bob',
                'team' => 1,
                'temp' => 0,
            ]);

        $this->assertResponseStatus(200);

        $this->seeInDatabase('players', [
            'name' => 'Bob',
            'temp' => 0,
        ]);
    }

    /** @test */
    public function admin_can_edit_visit_edit_player_view()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();
        $player = factory(App\Player::class)->create();

        $team->players()->attach($player);

        $this->actingAs($user)
            ->visit('/admin/players/' . $player->id . '/edit');

        $this->see('Edit player for ' . $team->name);
    }

    /** @test */
    public function admin_can_update_player()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();
        $player = factory(App\Player::class)->create();

        $team->players()->attach($player);

        $this->actingAs($user)
            ->call('PUT', '/admin/players/' . $player->id, [
                'name' => 'Bob',
                'temp' => 1,
            ]);

        // redirected to admin dashboard
        $this->assertRedirectedTo('/admin');

        $this->seeInDatabase('players', [
                'id' => $player->id,
                'name' => 'Bob',
                'temp' => 1,
            ]);
    }

    /** @test */
    public function admin_can_delete_player()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();
        $player = factory(App\Player::class)->create();

        $team->players()->attach($player);

        $this->actingAs($user)
            ->call('DELETE', '/admin/players/' . $player->id);

        $this->assertResponseStatus(200);

        $this->dontSeeInDatabase('players', [
                'id' => $player->id,
                'name' => $player->name,
            ]);
    }

    /** @test */
    public function admin_can_fetch_teams_for_player_use()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $teams = factory(App\Team::class, 5)->create();

        $teams->each(function ($team) use ($user) {
            $user->team()->save($team);
        });

        $this->actingAs($user)
            ->call('GET', '/admin/teams/fetch');

        $teams->each(function ($team) use($user) {
            $this->see('{"id":'. $team->id .',"user_id":'. $user->id .',"name":"'. $team->name .'","slug":"'. $team->slug .'","created_at":"'. $team->created_at .'","updated_at":"'. $team->updated_at .'"}');
        });
    }

    /** @test */
    public function admin_can_import_players_with_all_valid_data()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $teamA = factory(App\Team::class)->create(['name' => 'Team a', 'slug' => 'team-a']);
        $teamB = factory(App\Team::class)->create(['name' => 'Team b', 'slug' => 'team-b']);

        // TODO: need to implement, mock file upload
        // something like: new Symfony\Component\HttpFoundation\File\UploadedFile( storage_path( 'testing_files/AllValidPlayers.xlsx' ), 'test_AllValidPlayers.xlsx', 'application/vnd.ms-excel', 300, null, true );

        $this->assertTrue(true);
    }

    /** @test */
    public function admin_can_import_fixed_players_with_not_all_valid_data()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $teamA = factory(App\Team::class)->create(['name' => 'Team a', 'slug' => 'team-a']);
        $teamB = factory(App\Team::class)->create(['name' => 'Team b', 'slug' => 'team-b']);

        $this->actingAs($user)
            ->post('/admin/players/import/fixed', [
                'players' => [
                    [
                        'name' => 'Bob',
                        'team' => 3,
                    ],
                ]
            ])
            ->seeJson([
                'invalid_player_data' => [
                    [
                        'name' => 'Bob',
                        'team' => 3
                    ]
                ]
            ]);

            $this->dontSeeInDatabase('players', [
                'id' => 1,
                'name' => 'Bob',
             ]);

            $this->dontSeeInDatabase('player_team', [
                'player_id' => 1,
                'team_id' => 3,
             ]);
    }

    /** @test */
    public function admin_can_import_fixed_players_with_all_valid_data()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $teamA = factory(App\Team::class)->create(['name' => 'Team a', 'slug' => 'team-a']);
        $teamB = factory(App\Team::class)->create(['name' => 'Team b', 'slug' => 'team-b']);

        $this->actingAs($user)
            ->post('/admin/players/import/fixed', [
                'players' => [
                    [
                        'name' => 'Bob',
                        'team' => 1,
                    ],
                    [
                        'name' => 'Alice',
                        'team' => 2,
                    ]
                ]
            ])
            ->seeJson([
                'invalid_player_data' => []
            ]);

            $this->seeInDatabase('players', [
                'id' => 1,
                'name' => 'Bob',
             ]);

            $this->seeInDatabase('players', [
                'id' => 2,
                'name' => 'Alice',
             ]);

            $this->seeInDatabase('player_team', [
                'player_id' => 1,
                'team_id' => 1,
             ]);

            $this->seeInDatabase('player_team', [
                'player_id' => 2,
                'team_id' => 2,
             ]);
    }

   /**
    * Validation
    */

    /** @test */
    public function admin_must_provide_name_when_creating_new_player()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();

        $this->actingAs($user)
            ->call('POST', '/admin/players/new', [
                'team' => 1,
                'temp' => 0,
            ]);

        $this->assertResponseStatus(302);

        $this->dontSeeInDatabase('players', [
            'id' => 1,
            'temp' => 0,
        ]);

        $this->dontSeeInDatabase('player_team', [
            'player_id' => 1,
            'team_id' => $team->id,
        ]);
    }

    /** @test */
    public function admin_must_provide_team_when_creating_new_player()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();

        $this->actingAs($user)
            ->call('POST', '/admin/players/new', [
                'name' => 'Bob',
                'temp' => 0,
            ]);

        $this->assertResponseStatus(302);

        $this->dontSeeInDatabase('players', [
            'name' => 'Bob',
            'temp' => 0,
        ]);
    }

    /** @test */
    public function admin_doesnt_need_to_supply_a_temp_value_when_creating_new_player()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();

        $this->actingAs($user)
            ->call('POST', '/admin/players/new', [
                'name' => 'Bob',
                'team' => 1,
            ]);

        $this->assertResponseStatus(200);

        $this->seeInDatabase('players', [
            'name' => 'Bob',
            'temp' => 0,
        ]);

        $this->seeInDatabase('player_team', [
            'player_id' => 1,
            'team_id' => $team->id,
        ]);
    }

    /** @test */
    public function team_exists_validation_works_when_admin_creating_new_player()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);

        $this->actingAs($user)
            ->call('POST', '/admin/players/new', [
                'name' => 'Bob',
                'team' => 99,
                'temp' => 1,
            ]);

        $this->assertResponseStatus(302);

        $this->dontSeeInDatabase('players', [
            'name' => 'Bob',
            'temp' => 1,
        ]);

        $this->dontSeeInDatabase('player_team', [
            'player_id' => 1,
            'team_id' => 99,
        ]);
    }

    /** @test */
    public function admin_must_submit_players_for_import_fixed_data()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $teamA = factory(App\Team::class)->create(['name' => 'Team a', 'slug' => 'team-a']);
        $teamB = factory(App\Team::class)->create(['name' => 'Team b', 'slug' => 'team-b']);

        $this->actingAs($user)
            ->post('/admin/players/import/fixed', [
                //
            ]);

        // validation redirection
        $this->assertResponseStatus(302);
    }
}
