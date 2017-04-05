<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminPlayerTest extends BrowserKitTestCase
{

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
            $this->see('{"id":' . $player->id . ',"name":"' . $player->name . '","temp":0,"recent":0,"rounds":0}');
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
    public function admin_can_visit_edit_player_view()
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
    public function admin_cannot_delete_player_if_player_has_played_in_rounds()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();
        $player = factory(App\Player::class)->create();
        $round = factory(App\Round::class)->create();

        $team->players()->attach($player);
        $team->rounds()->attach($round);
        $player->rounds()->attach($round);

        $this->actingAs($user)
            ->call('DELETE', '/admin/players/' . $player->id);

        $this->assertResponseStatus(400);

        $this->seeInDatabase('players', [
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
            $user->teams()->save($team);
        });

        $this->actingAs($user)
            ->call('GET', '/admin/teams/fetch');

        $teams->each(function ($team) use($user) {
            $this->see('{"id":'. $team->id .',"user_id":'. $user->id .',"name":"'. $team->name .'","slug":"'. $team->slug .'","created_at":"'. $team->created_at .'","updated_at":"'. $team->updated_at .'","best_players_allowed":1}');
        });
    }

    /** @test */
    public function admin_can_import_players_with_all_valid_data()
    {
        // create test file
        copy(storage_path('test_files/master_files/AllValidPlayers.xlsx'), storage_path('test_files/Players.xlsx'));

        /**
         * contents of AllValidPlayers.xlsx
         *
         *  Player Name  Team
         *  Ashley 1    Team x
         *  Ashley 2    Team x
         *  Ashley 3    Team y
         */

        $user = factory(App\User::class)->create(['role' => 'admin']);
        $teamX = factory(App\Team::class)->create(['name' => 'Team x', 'slug' => 'team-x']);
        $teamY = factory(App\Team::class)->create(['name' => 'Team y', 'slug' => 'team-y']);

        $file = new Illuminate\Http\UploadedFile(
                storage_path('test_files/Players.xlsx'),
                'Players.xlsx',
                'application/vnd.ms-excel',
                null,
                null,
                true
            );

       $response = $this->actingAs($user)
            ->call('POST', '/admin/players/import', [], [], [
                'players' => $file
            ]);

        $this->assertTrue($response->content() == '{"invalid_player_data":[]}');

        $this->seeInDatabase('players', [
            'name' => 'Ashley 1',
        ]);
        $this->seeInDatabase('players', [
            'name' => 'Ashley 2',
        ]);
        $this->seeInDatabase('players', [
            'name' => 'Ashley 3',
        ]);

        $this->seeInDatabase('player_team', [
            'team_id' => $teamX->id,
            'player_id' => 1,
        ]);
        $this->seeInDatabase('player_team', [
            'team_id' => $teamX->id,
            'player_id' => 2,
        ]);
        $this->seeInDatabase('player_team', [
            'team_id' => $teamY->id,
            'player_id' => 3,
        ]);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function admin_can_attempt_to_import_players_with_all_invalid_data()
    {
        // create test file
        copy(storage_path('test_files/master_files/AllInValidPlayers.xlsx'), storage_path('test_files/Players.xlsx'));

        /**
         * contents of AllInValidPlayers.xlsx
         *
         *  Player Name  Team
         *  Ashley 1    Team a
         *  Ashley 2    Team a
         *  Ashley 3    Team b
         */

        $user = factory(App\User::class)->create(['role' => 'admin']);
        $teamX = factory(App\Team::class)->create(['name' => 'Team x', 'slug' => 'team-x']);
        $teamY = factory(App\Team::class)->create(['name' => 'Team y', 'slug' => 'team-y']);

        $file = new Illuminate\Http\UploadedFile(
                storage_path('test_files/Players.xlsx'),
                'Players.xlsx',
                'application/vnd.ms-excel',
                null,
                null,
                true
            );

       $response = $this->actingAs($user)
            ->call('POST', '/admin/players/import', [], [], [
                'players' => $file
            ]);

        $this->assertTrue($response->content() == '{"invalid_player_data":[{"team":"Team a","name":"Ashley 1"},{"team":"Team a","name":"Ashley 2"},{"team":"Team b","name":"Ashley 3"}]}');

        $this->dontSeeInDatabase('players', [
            'name' => 'Ashley 1',
        ]);
        $this->dontSeeInDatabase('players', [
            'name' => 'Ashley 2',
        ]);
        $this->dontSeeInDatabase('players', [
            'name' => 'Ashley 3',
        ]);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function admin_can_import_players_with_some_valid_and_some_invalid_data()
    {
        // create test file
        copy(storage_path('test_files/master_files/ValidWithInValidPlayers.xlsx'), storage_path('test_files/Players.xlsx'));

        /**
         * contents of ValidWithInValidPlayers.xlsx
         *
         *  Player Name  Team
         *  Ashley 1    Team x
         *  Ashley 2    Team a
         *  Ashley 3    Team b
         */

        $user = factory(App\User::class)->create(['role' => 'admin']);
        $teamX = factory(App\Team::class)->create(['name' => 'Team x', 'slug' => 'team-x']);
        $teamY = factory(App\Team::class)->create(['name' => 'Team y', 'slug' => 'team-y']);

        $file = new Illuminate\Http\UploadedFile(
                storage_path('test_files/Players.xlsx'),
                'Players.xlsx',
                'application/vnd.ms-excel',
                null,
                null,
                true
            );

       $response = $this->actingAs($user)
            ->call('POST', '/admin/players/import', [], [], [
                'players' => $file
            ]);

        $this->assertTrue($response->content() == '{"invalid_player_data":[{"team":"Team a","name":"Ashley 2"},{"team":"Team b","name":"Ashley 3"}]}');

        $this->seeInDatabase('players', [
            'name' => 'Ashley 1',
        ]);

        $this->dontSeeInDatabase('players', [
            'name' => 'Ashley 2',
        ]);
        $this->dontSeeInDatabase('players', [
            'name' => 'Ashley 3',
        ]);

        $this->seeInDatabase('player_team', [
            'team_id' => $teamX->id,
            'player_id' => 1,
        ]);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function admin_gets_error_when_attempting_to_import_empty_file()
    {
        // create test file
        copy(storage_path('test_files/master_files/Empty.xlsx'), storage_path('test_files/Players.xlsx'));

        $user = factory(App\User::class)->create(['role' => 'admin']);
        $teamX = factory(App\Team::class)->create(['name' => 'Team x', 'slug' => 'team-x']);
        $teamY = factory(App\Team::class)->create(['name' => 'Team y', 'slug' => 'team-y']);

        $file = new Illuminate\Http\UploadedFile(
                storage_path('test_files/Players.xlsx'),
                'Players.xlsx',
                'application/vnd.ms-excel',
                null,
                null,
                true
            );

       $this->actingAs($user)
            ->call('POST', '/admin/players/import', [], [], [
                'players' => $file
            ]);


        $this->assertResponseStatus(400);
    }

    /** @test */
    public function admin_gets_error_when_attempting_to_import_file_that_is_not_in_correct_format()
    {
        // create test file
        copy(storage_path('test_files/master_files/IncorrectFormat.xlsx'), storage_path('test_files/Players.xlsx'));

        /**
         * Contents of IncorrectFormat.xlsx
         *
         * Some Header  Some other header
         *   1          2
         *   1          2
         *   1          2
         */

        $user = factory(App\User::class)->create(['role' => 'admin']);
        $teamX = factory(App\Team::class)->create(['name' => 'Team x', 'slug' => 'team-x']);
        $teamY = factory(App\Team::class)->create(['name' => 'Team y', 'slug' => 'team-y']);

        $file = new Illuminate\Http\UploadedFile(
                storage_path('test_files/Players.xlsx'),
                'Players.xlsx',
                'application/vnd.ms-excel',
                null,
                null,
                true
            );

       $this->actingAs($user)
            ->call('POST', '/admin/players/import', [], [], [
                'players' => $file
            ]);


        $this->assertResponseStatus(400);
    }

    /** @test */
    public function admin_can_attempt_to_import_fixed_players_with_not_all_valid_data()
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

        $this->assertResponseStatus(200);
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
    public function admin_gets_validation_error_if_players_is_not_a_file()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $teamX = factory(App\Team::class)->create(['name' => 'Team x', 'slug' => 'team-x']);
        $teamY = factory(App\Team::class)->create(['name' => 'Team y', 'slug' => 'team-y']);

       $this->actingAs($user)
            ->call('POST', '/admin/players/import', [], [], [
                'players' => 'string'
            ]);

        // validation redirection
        $this->assertResponseStatus(302);
    }

    /** @test */
    public function admin_gets_validation_error_if_players_is_not_available()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $teamX = factory(App\Team::class)->create(['name' => 'Team x', 'slug' => 'team-x']);
        $teamY = factory(App\Team::class)->create(['name' => 'Team y', 'slug' => 'team-y']);

       $this->actingAs($user)
            ->call('POST', '/admin/players/import', [], [], [
                //
            ]);

        // validation redirection
        $this->assertResponseStatus(302);
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
