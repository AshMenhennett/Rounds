<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminRoundTest extends BrowserKitTestCase
{

    /** @test */
    public function admin_can_fetch_paginated_rounds()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $rounds = factory(App\Round::class, 5)->create();
        $team = factory(App\Team::class)->create();

        $rounds->each(function ($round) use ($team) {
            $team->rounds()->attach($round);
        });

        $this->actingAs($user)
            ->call('GET', '/admin/rounds/fetch');

        $rounds->each(function ($round) {
            $this->see('{"id":' . $round->id . ',"name":"' . $round->name . '","default_date":"'. Carbon\Carbon::parse($round->default_date)->toDateString() .'","teams":1}');
        });

        $this->see('"meta":{"pagination":{"total":5,"count":5,"per_page":10,"current_page":1,"total_pages":1,"links":[]}}');
    }

    /** @test */
    public function admin_can_create_new_round()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);

        $date = Carbon\Carbon::now();

        $this->actingAs($user)
            ->call('POST', '/admin/rounds/new', [
                'name' => '2a',
                'date' => $date,
            ]);

        $this->assertResponseStatus(200);

        $this->seeInDatabase('rounds', [
            'name' => '2a',
            'default_date' => $date,
        ]);
    }

    /** @test */
    public function admin_can_visit_edit_round_view()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $round = factory(App\Round::class)->create();

        $this->actingAs($user)
            ->visit('/admin/rounds/' . $round->id . '/edit');

        $this->see('Edit round ' . $round->name);
    }

    /** @test */
    public function admin_can_update_round()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $round = factory(App\Round::class)->create();

        $date = Carbon\Carbon::now()->toDateString();

        $this->actingAs($user)
            ->call('PUT', '/admin/rounds/' . $round->id, [
                'name' => '2a',
                'date' => $date,
            ]);

        $this->seeInDatabase('rounds', [
                'id' => $round->id,
                'name' => '2a',
                'default_date' => $date,
            ]);
    }

    /** @test */
    public function admin_can_delete_round()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $round = factory(App\Round::class)->create();

        $this->actingAs($user)
            ->call('DELETE', '/admin/rounds/' . $round->id);

        $this->assertResponseStatus(200);

        $this->dontSeeInDatabase('rounds', [
                'id' => $round->id,
                'name' => $round->name,
                'default_date' => $round->default_date,
            ]);
    }

    /** @test */
    public function admin_cannot_delete_round_if_round_has_teams()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $team = factory(App\Team::class)->create();
        $round = factory(App\Round::class)->create();

        $team->rounds()->attach($round);

        $this->actingAs($user)
            ->call('DELETE', '/admin/rounds/' . $round->id);

        $this->assertResponseStatus(400);

        $this->seeInDatabase('rounds', [
                'id' => $round->id,
                'name' => $round->name,
                'default_date' => $round->default_date,
            ]);
    }

    /** @test */
    public function admin_can_import_rounds_with_all_valid_data()
    {
        // create test file
        copy(storage_path('test_files/master_files/AllValidRounds.xlsx'), storage_path('test_files/Rounds.xlsx'));

        /**
         * contents of AllValidRounds.xlsx
         *
         *  Round    Date
         *  1        25/12/17
         *  2        17/06/89
         */

        $user = factory(App\User::class)->create(['role' => 'admin']);

        $file = new Illuminate\Http\UploadedFile(
                storage_path('test_files/Rounds.xlsx'),
                'Rounds.xlsx',
                'application/vnd.ms-excel',
                null,
                null,
                true
            );

       $response = $this->actingAs($user)
            ->call('POST', '/admin/rounds/import', [], [], [
                'rounds' => $file
            ]);

        $this->assertTrue($response->content() == '{"invalid_round_data":[]}');

        $this->seeInDatabase('rounds', [
            'name' => '1',
            'default_date' => Carbon\Carbon::parse('25-12-2017'),
        ]);
        $this->seeInDatabase('rounds', [
            'name' => '2',
            'default_date' => Carbon\Carbon::parse('17-06-1989')
        ]);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function admin_can_attempt_to_import_players_with_all_invalid_data()
    {
        // create test file
        copy(storage_path('test_files/master_files/AllInValidRounds.xlsx'), storage_path('test_files/Rounds.xlsx'));

        /**
         * contents of AllInValidRounds.xlsx
         *
         *  Round    Date
         *           25/12/17
         *  2
         */

        $user = factory(App\User::class)->create(['role' => 'admin']);

        $file = new Illuminate\Http\UploadedFile(
                storage_path('test_files/Rounds.xlsx'),
                'Rounds.xlsx',
                'application/vnd.ms-excel',
                null,
                null,
                true
            );

       $response = $this->actingAs($user)
            ->call('POST', '/admin/rounds/import', [], [], [
                'rounds' => $file
            ]);

        $this->assertTrue($response->content() == '{"invalid_round_data":[{"date":{"date":"2017-12-25 00:00:00.000000","timezone_type":3,"timezone":"UTC"},"name":null},{"date":null,"name":2}]}');

        $this->dontSeeInDatabase('rounds', [
            'default_date' => "2017-12-25 00:00:00",
        ]);
        $this->dontSeeInDatabase('rounds', [
            'name' => '2',
        ]);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function admin_can_import_rounds_with_some_valid_and_some_invalid_data()
    {
        // create test file
        copy(storage_path('test_files/master_files/ValidWithInvalidRounds.xlsx'), storage_path('test_files/Rounds.xlsx'));

        /**
         * contents of ValidWithInvalidRounds.xlsx
         *
         *  Round   Date
         *   1      25/12/17
         *   2      17/6/89
         *   3
         */

        $user = factory(App\User::class)->create(['role' => 'admin']);

        $file = new Illuminate\Http\UploadedFile(
                storage_path('test_files/Rounds.xlsx'),
                'Rounds.xlsx',
                'application/vnd.ms-excel',
                null,
                null,
                true
            );

       $response = $this->actingAs($user)
            ->call('POST', '/admin/rounds/import', [], [], [
                'rounds' => $file
            ]);

        $this->assertTrue($response->content() == '{"invalid_round_data":[{"date":null,"name":3}]}');

        $this->seeInDatabase('rounds', [
            'name' => '1',
            'default_date' => Carbon\Carbon::parse('25-12-2017'),
        ]);

        $this->seeInDatabase('rounds', [
            'name' => '2',
            'default_date' => Carbon\Carbon::parse('17-06-1989'),
        ]);
        $this->dontSeeInDatabase('rounds', [
            'name' => '3',
        ]);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function admin_gets_error_when_attempting_to_import_empty_file()
    {
        // create test file
        copy(storage_path('test_files/master_files/Empty.xlsx'), storage_path('test_files/Rounds.xlsx'));

        $user = factory(App\User::class)->create(['role' => 'admin']);

        $file = new Illuminate\Http\UploadedFile(
                storage_path('test_files/Rounds.xlsx'),
                'Rounds.xlsx',
                'application/vnd.ms-excel',
                null,
                null,
                true
            );

       $this->actingAs($user)
            ->call('POST', '/admin/rounds/import', [], [], [
                'rounds' => $file
            ]);

        $this->assertResponseStatus(400);
    }

    /** @test */
    public function admin_gets_validation_error_when_attempting_to_post_no_data()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);

       $this->actingAs($user)
            ->call('POST', '/admin/rounds/import', [], [], [
                //
            ]);

        // redirected
        $this->assertResponseStatus(302);
    }

    /** @test */
    public function admin_gets_error_when_attempting_to_import_file_that_is_not_in_correct_format()
    {
        // create test file
        copy(storage_path('test_files/master_files/IncorrectFormat.xlsx'), storage_path('test_files/Rounds.xlsx'));

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
                storage_path('test_files/Rounds.xlsx'),
                'Rounds.xlsx',
                'application/vnd.ms-excel',
                null,
                null,
                true
            );

       $this->actingAs($user)
            ->call('POST', '/admin/rounds/import', [], [], [
                'rounds' => $file
            ]);


        $this->assertResponseStatus(400);
    }

    /** @test */
    public function admin_can_import_fixed_rounds_with_not_all_valid_data()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);

        $this->actingAs($user)
            ->post('/admin/rounds/import/fixed', [
                'rounds' => [
                    [
                        'name' => '2a',
                        'date' => 'string',
                    ],
                    [
                        'name' => '3a',
                        'date' => '17-06-1989',
                    ],
                ]
            ])->seeJson([
                'invalid_round_data' => [
                    [
                        'name' => '2a',
                        'date' => null,
                    ]
                ]
            ]);

            $this->seeInDatabase('rounds', [
                'name' => '3a',
                'default_date' => Carbon\Carbon::parse('17-06-1989'),
             ]);

            $this->dontSeeInDatabase('rounds', [
                'default_date' => 'string',
             ]);

    }

    /** @test */
    public function admin_can_import_fixed_rounds_with_all_invalid_data()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);

        $this->actingAs($user)
            ->post('/admin/rounds/import/fixed', [
                'rounds' => [
                    [
                        'name' => '2a',
                        'date' => 'string'
                    ],
                    [
                        'name' => '3a',
                        'date' => 'string'
                    ],
                ]
            ])->seeJson([
                'invalid_round_data' => [
                    [
                        'name' => '2a',
                        'date' => null
                    ],
                    [
                        'name' => '3a',
                        'date' => null
                    ]
                ]
            ]);

            $this->dontSeeInDatabase('rounds', [
                'name' => '2a',
             ]);

            $this->dontSeeInDatabase('rounds', [
                'name' => '3a',
             ]);

    }

    /** @test */
    public function admin_can_import_fixed_rounds_with_all_valid_data()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);

        $date1 = Carbon\Carbon::now();
        $date2 = Carbon\Carbon::now()->addWeek();

        $this->actingAs($user)
            ->post('/admin/rounds/import/fixed', [
                'rounds' => [
                    [
                        'name' => '1b',
                        'date' => $date1,
                    ],
                    [
                        'name' => '2a',
                        'date' => $date2,
                    ]
                ]
            ])
            ->seeJson([
                'invalid_round_data' => []
            ]);

            $this->seeInDatabase('rounds', [
                'id' => 1,
                'name' => '1b',
                'default_date' => $date1->toDateTimeString(),
             ]);

            $this->seeInDatabase('rounds', [
                'id' => 2,
                'name' => '2a',
                'default_date' => $date2->toDateTimeString(),
             ]);

        $this->assertResponseStatus(200);
    }

   /**
    * Validation
    */

    /** @test */
    public function admin_must_provide_name_and_date_when_creating_new_round()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);
        $date = Carbon\Carbon::now();
        $this->actingAs($user)
            ->call('POST', '/admin/rounds/new', [
                'name' => '1a',
                // no date
            ]);

        $this->assertResponseStatus(302);

        $this->dontSeeInDatabase('rounds', [
            'name' => '1a',
        ]);

        $this->actingAs($user)
            ->call('POST', '/admin/rounds/new', [
                'date' => $date->toDateString(),
                // no name
            ]);

        $this->assertResponseStatus(302);

        $this->dontSeeInDatabase('rounds', [
            'default_date' => $date->toDateString(),
        ]);

    }

    /** @test */
    public function admin_gets_validation_error_if_rounds_is_not_a_file_when_attempting_to_import()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);

       $this->actingAs($user)
            ->call('POST', '/admin/rounds/import', [], [], [
                'rounds' => 'string'
            ]);

        // validation redirection
        $this->assertResponseStatus(302);
    }

    /** @test */
    public function admin_gets_validation_error_if_rounds_is_not_available_when_attempting_to_import()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);

       $this->actingAs($user)
            ->call('POST', '/admin/rounds/import', [], [], [
                //
            ]);

        // validation redirection
        $this->assertResponseStatus(302);
    }

    /** @test */
    public function admin_must_submit_round_for_import_fixed_data()
    {
        $user = factory(App\User::class)->create(['role' => 'admin']);

        $this->actingAs($user)
            ->post('/admin/rounds/import/fixed', [
                //
            ]);

        // validation redirection
        $this->assertResponseStatus(302);
    }
}
