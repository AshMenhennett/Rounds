<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminTeamTest extends BrowserKitTestCase
{

    /** @test */
    public function admin_can_fetch_teams()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $teams = factory(App\Team::class, 5)->create();

        $this->actingAs($user)
            ->call('GET', '/admin/teams/fetch');

        $teams->each(function ($team) {
            $this->see('{"id":' . $team->id . ',"user_id":null,"name":"' . $team->name . '","slug":"' . $team->slug . '","created_at":"'. $team->created_at .'","updated_at":"'. $team->updated_at .'","best_players_allowed":1}');
        });
    }

    /** @test */
    public function admin_can_fetch_paginated_teams()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $teams = factory(App\Team::class, 5)->create();

        $this->actingAs($user)
            ->call('GET', '/admin/teams/fetch/pagination');

        $teams->each(function ($team) {
            $this->see('{"id":' . $team->id . ',"name":"' . $team->name . '","slug":"' . $team->slug . '","hasCoach":0,"players":0}');
        });

        $this->see('"meta":{"pagination":{"total":5,"count":5,"per_page":10,"current_page":1,"total_pages":1,"links":[]}}');
    }

    /** @test */
    public function admin_can_create_new_team()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);

        $this->actingAs($user)
            ->call('POST', '/admin/teams/new', [
                'name' => 'The Bears',
            ]);

        $this->assertResponseStatus(200);

        $this->seeInDatabase('teams', [
            'name' => 'The Bears',
            'slug' => 'the-bears',
        ]);
    }

    /** @test */
    public function admin_can_visit_edit_team_view()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();

        $this->actingAs($user)
            ->visit('/admin/teams/' . $team->slug . '/edit');

        $this->see('Edit ' . $team->name);
    }

    /** @test */
    public function admin_can_update_team()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();

        $this->actingAs($user)
            ->call('PUT', '/admin/teams/' . $team->slug, [
                'name' => 'The Bears',
                'slug' => 'dabearz'
            ]);

        $this->seeInDatabase('teams', [
                'id' => $team->id,
                'name' => 'The Bears',
                'slug' => 'dabearz',
            ]);
    }

    /** @test */
    public function admin_can_delete_team()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();

        $this->actingAs($user)
            ->call('DELETE', '/admin/teams/' . $team->slug);

        $this->assertResponseStatus(200);

        $this->dontSeeInDatabase('teams', [
                'id' => $team->id,
                'name' => $team->name,
                'slug' => $team->slug,
            ]);
    }

    /** @test */
    public function admin_cannot_delete_team_if_team_has_players()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();
        $player = factory(App\Player::class)->create();

        $team->players()->attach($player);

        $this->actingAs($user)
            ->call('DELETE', '/admin/teams/' . $team->slug);

        $this->assertResponseStatus(400);

        $this->seeInDatabase('teams', [
                'id' => $team->id,
                'name' => $team->name,
            ]);
    }


    /** @test */
    public function admin_can_import_teams_with_all_valid_data()
    {
        // create test file
        copy(storage_path('test_files/master_files/AllValidTeams.xlsx'), storage_path('test_files/Teams.xlsx'));

        /**
         * contents of AllValidTeams.xlsx
         *
         *  Team Name
         *  The Bears
         *  The Jack Russels
         *  The Mice
         */

        $user = factory(App\User::class)->create(['role' => 'admin']);

        $file = new Illuminate\Http\UploadedFile(
                storage_path('test_files/Teams.xlsx'),
                'Teams.xlsx',
                'application/vnd.ms-excel',
                null,
                null,
                true
            );

       $response = $this->actingAs($user)
            ->call('POST', '/admin/teams/import', [], [], [
                'teams' => $file
            ]);

        // response is not the HTTP request, rather this test
        //$this->assertTrue($response->content() == '{"invalid_team_data":[],{"valid_team_data":["The Bears", "The Jack Russels", "The Mice"]}');

        $this->seeInDatabase('teams', [
            'name' => 'The Bears',
        ]);
        $this->seeInDatabase('teams', [
            'name' => 'The Jack Russels',
        ]);
        $this->seeInDatabase('teams', [
            'name' => 'The Mice',
        ]);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function admin_can_attempt_to_import_teams_with_all_invalid_data()
    {
        // create test file
        copy(storage_path('test_files/master_files/AllInValidTeams.xlsx'), storage_path('test_files/Teams.xlsx'));

        /**
         * contents of AllInValidTeams.xlsx
         *
         *  Team Name
         *  The Bears
         *  Tiger/Tiger
         */

        $user = factory(App\User::class)->create(['role' => 'admin']);
        $teamA = factory(App\Team::class)->create(['name' => 'The Bears']);
        $teamB = factory(App\Team::class)->create(['name' => 'Tiger\Tiger', 'slug' => 'tigertiger']);

        $file = new Illuminate\Http\UploadedFile(
                storage_path('test_files/Teams.xlsx'),
                'Teams.xlsx',
                'application/vnd.ms-excel',
                null,
                null,
                true
            );

       $response = $this->actingAs($user)
            ->call('POST', '/admin/teams/import', [], [], [
                'teams' => $file
            ]);

        $this->assertTrue($response->content() == '{"invalid_team_data":[{"name":"The Bears"},{"name":"Tiger\/Tiger"}],"valid_team_data":[]}');

        $this->dontSeeInDatabase('teams', [
            // invalid as team already exists
            'id' => 3,
            'name' => 'The Bears',
        ]);
        $this->dontSeeInDatabase('teams', [
            // invalid as slug that will be computed will be the same as an already existing team (TeamB)
            'id' => 4,
            'name' => 'Tiger/Tiger',
        ]);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function admin_can_attempt_to_import_team_with_some_valid_and_some_invalid_data()
    {
        // create test file
        copy(storage_path('test_files/master_files/ValidWithInValidTeams.xlsx'), storage_path('test_files/Teams.xlsx'));

        /**
         * contents of ValidWithInValidTeams.xlsx
         *
         *  Team Name
         *  The Bears
         *  The Jack Russels
         */

        $user = factory(App\User::class)->create(['role' => 'admin']);
        $teamA = factory(App\Team::class)->create(['name' => 'The Bears']);

        $file = new Illuminate\Http\UploadedFile(
                storage_path('test_files/Teams.xlsx'),
                'Teams.xlsx',
                'application/vnd.ms-excel',
                null,
                null,
                true
            );

       $response = $this->actingAs($user)
            ->call('POST', '/admin/teams/import', [], [], [
                'teams' => $file
            ]);

        $this->assertTrue($response->content() == '{"invalid_team_data":[{"name":"The Bears"}],"valid_team_data":[{"name":"The Jack Russels"}]}');

        $this->seeInDatabase('teams', [
            'id' => 2,
            'name' => 'The Jack Russels',
        ]);

        $this->dontSeeInDatabase('teams', [
            // invalid as team already exists
            'id' => 2,
            'name' => 'The Bears',
        ]);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function admin_gets_error_when_attempting_to_import_empty_file()
    {
        // create test file
        copy(storage_path('test_files/master_files/Empty.xlsx'), storage_path('test_files/Teams.xlsx'));

        $user = factory(App\User::class)->create(['role' => 'admin']);

        $file = new Illuminate\Http\UploadedFile(
                storage_path('test_files/Teams.xlsx'),
                'Teams.xlsx',
                'application/vnd.ms-excel',
                null,
                null,
                true
            );

       $this->actingAs($user)
            ->call('POST', '/admin/teams/import', [], [], [
                'teams' => $file
            ]);

        $this->assertResponseStatus(400);
    }

    /** @test */
    public function admin_gets_error_when_attempting_to_import_file_that_is_not_in_correct_format()
    {
        // create test file
        copy(storage_path('test_files/master_files/IncorrectFormat.xlsx'), storage_path('test_files/Teams.xlsx'));

        /**
         * Contents of IncorrectFormat.xlsx
         *
         * Some Header  Some other header
         *   1          2
         *   1          2
         *   1          2
         */

        $user = factory(App\User::class)->create(['role' => 'admin']);

        $file = new Illuminate\Http\UploadedFile(
                storage_path('test_files/Teams.xlsx'),
                'Teams.xlsx',
                'application/vnd.ms-excel',
                null,
                null,
                true
            );

       $this->actingAs($user)
            ->call('POST', '/admin/teams/import', [], [], [
                'teams' => $file
            ]);

        $this->assertResponseStatus(400);
    }

    /** @test */
    public function admin_can_set_best_players_allowed_status()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create(['best_players_allowed' => 0]);

        $this->actingAs($user)
            ->call('PUT', '/admin/teams/' . $team->slug . '/bestPlayersAllowed/toggle');

        $this->seeInDatabase('teams', [
            'id' => $team->id,
            'best_players_allowed' => 1
        ]);

        $this->actingAs($user)
            ->call('PUT', '/admin/teams/' . $team->slug . '/bestPlayersAllowed/toggle');

        $this->seeInDatabase('teams', [
            'id' => $team->id,
            'best_players_allowed' => 0
        ]);
    }

   /**
    * Validation
    */

    /** @test */
    public function admin_must_provide_name_when_creating_new_team()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);

        $this->actingAs($user)
            ->call('POST', '/admin/teams/new', [
                //
            ]);

        // validation redirection
        $this->assertResponseStatus(302);
    }

    /** @test */
    public function admin_must_provide_a_unique_name_when_creating_new_team()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create(['name' => 'The Bears']);

        $this->actingAs($user)
            ->call('POST', '/admin/teams/new', [
                'name' => 'The Bears'
            ]);

        // validation redirection
        $this->assertResponseStatus(302);

        $this->dontSeeInDatabase('teams', [
            'id' => 2,
            'name' => 'The Bears'
        ]);
    }

    /** @test */
    public function admin_must_provide_a_unique_name_with_unique_slug_when_creating_new_team()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create(['name' => 'The/Bears']);

        $this->actingAs($user)
            ->call('POST', '/admin/teams/new', [
                'name' => 'The\Bears'
            ]);

        // validation redirection
        $this->assertResponseStatus(302);

        $this->dontSeeInDatabase('teams', [
            'name' => 'The\Bears',
            'slug' => 'thebears'
        ]);
    }

    /** @test */
    public function admin_gets_validation_error_if_teams_is_not_a_file()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);

       $this->actingAs($user)
            ->call('POST', '/admin/teams/import', [], [], [
                'teams' => 'string'
            ]);

        // validation redirection
        $this->assertResponseStatus(302);
    }

    /** @test */
    public function admin_gets_validation_error_if_teams_is_not_available()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);

       $this->actingAs($user)
            ->call('POST', '/admin/teams/import', [], [], [
                //
            ]);

        // validation redirection
        $this->assertResponseStatus(302);
    }

}
