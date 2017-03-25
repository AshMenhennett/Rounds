<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CoachRoundsTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_can_see_available_rounds()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->visit('/teams/' . $team->slug . '/rounds');

        $rounds->each(function ($round) {
            $this->see('Fill in Round ' . $round->name);
        });
    }

    /** @test */
    public function user_can_store_date_for_a_round()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id . '/date', [
                'date' => '2018-01-03',
            ]);

        $this->seeInDatabase('round_team', [
            'team_id' => $team->id,
            'round_id' => $rounds->first()->id,
            'date' => Carbon\Carbon::parse('2018-01-03')->toDateTimeString(),
        ]);
    }

    /** @test */
    public function user_can_see_default_round_dates()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->visit('/teams/' . $team->slug . '/rounds');

        $rounds->each(function ($round) {
            $this->see('Fill in Round ' . $round->name)
                ->see('<strong>Date</strong>: ' . \Carbon\Carbon::createFromTimeStamp(strtotime($round->default_date))->format('d/m/y'));
        });
    }

    /** @test */
    public function user_can_see_custom_round_dates()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $user->team()->save($team);
        $rounds->each(function ($round) use (&$team) {
            $team->rounds()->attach($round, [
                'date' => '2018-01-12 02:26:03',
            ]);
        });

        $this->seeInDatabase('round_team', [
            'team_id' => $team->id,
            'date' => '2018-01-12 02:26:03',
        ]);

        $this->actingAs($user)
            ->visit('/teams/' . $team->slug . '/rounds');

        $rounds->each(function ($round) {
            $this->see('Fill in Round ' . $round->name )
                ->see('<strong>Date</strong>: ' . \Carbon\Carbon::createFromTimeStamp(strtotime('2018-01-12 02:26:03'))->format('d/m/y'));
        });
    }

    /** @test */
    public function user_can_access_view_to_fill_in_round_data()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $round = factory(App\Round::class)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $round->id);

        $this->assertRedirectedTo('/teams/' . $team->slug . '/rounds/' . $round->id);
    }

    /** @test */
    public function user_can_save_association_between_team_and_round()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $round = factory(App\Round::class)->create();

        $user->team()->save($team);

        // initial store
        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $round->id);

         $this->seeInDatabase('round_team', [
                'id' => 1,
                'round_id' => $round->id,
                'team_id' => $team->id,
                'date' => NULL,
            ]);

         // subsequent store- round should not be saved again
         $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $round->id);

         $this->seeInDatabase('round_team', [
                'id' => 1,
                'round_id' => $round->id,
                'team_id' => $team->id,
                'date' => NULL,
            ]);

         $this->dontSeeInDatabase('round_team', [
                'id' => 2,
                'round_id' => $round->id,
                'team_id' => $team->id,
                'date' => NULL,
            ]);
    }

    /** @test */
    public function user_can_update_round_input()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();
        $players = factory(App\Player::class, 5)->create();

        $user->team()->save($team);

        $players->each(function ($player) use ($team) {
            $team->players()->attach($player);
        });

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        // submit data
        $this->actingAs($user)
            ->call('PUT', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id, [
                'players' =>
                    [
                        [
                            'id' => $players->first()->id,
                            'round' => [
                                'best_player' => 1,
                                'second_best_player' => 0,
                                'quarters' => [
                                    'count' => 4,
                                    'reason' => 'No reason'
                                ]
                            ],
                        ],
                        [
                            'id' => $players->last()->id,
                            'round' => [
                                'best_player' => 0,
                                'second_best_player' => 1,
                                'quarters' => [
                                    'count' => 3,
                                    'reason' => 'Ill'
                                ]
                            ]
                        ]
                    ]
                ]);

        $this->seeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->first()->id,
            'best_player' => 1,
            'second_best_player' => 0,
            'quarters' => 4,
            'quarters_reason' => 'No reason'
        ]);

        $this->seeInDatabase('players', [
            'id' => $players->first()->id,
            'recent' => 1,
        ]);

        $this->seeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->last()->id,
            'best_player' => 0,
            'second_best_player' => 1,
            'quarters' => 3,
            'quarters_reason' => 'Ill',
        ]);

        $this->seeInDatabase('players', [
            'id' => $players->last()->id,
            'recent' => 1,
        ]);

        //update data
        $this->actingAs($user)
            ->call('PUT', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id, [
                'players' =>
                    [
                        [
                            'id' => $players->find(2)->id,
                            'round' => [
                                'best_player' => 0,
                                'second_best_player' => 0,
                                'quarters' => [
                                    'count' => 1,
                                    'reason' => NULL
                                ]
                            ],
                        ],
                        [
                            'id' => $players->last()->id,
                            'round' => [
                                'best_player' => 1,
                                'second_best_player' => 0,
                                'quarters' => [
                                    'count' => 2,
                                    'reason' => NULL
                                ],
                            ]
                        ]
                    ]
                ]);

        // check obselete player is not in player_round assoc
        $this->dontSeeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->first()->id,
        ]);

        // check obsolete player is no longer flagged as recent
        $this->dontSeeInDatabase('players', [
            'id' => $players->first()->id,
            'recent' => 1,
        ]);

        // checking new player in player_round
        $this->seeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->find(2)->id,
            'best_player' => 0,
            'second_best_player' => 0,
            'quarters' => 1,
            'quarters_reason' => NULL
        ]);

        // check new player is flagged as recent
        $this->seeInDatabase('players', [
            'id' => $players->find(2)->id,
            'recent' => 1,
        ]);

        // check existing player is updated and recent
         $this->seeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->last()->id,
            'best_player' => 1,
            'second_best_player' => 0,
            'quarters' => 2,
            'quarters_reason' => NULL,
        ]);

         $this->seeInDatabase('players', [
            'id' => $players->last()->id,
            'recent' => 1,
        ]);
    }

    /** @test */
    public function player_round_data_as_array_in_team_is_correct()
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
            ->visit('/teams/' . $team->slug . '/rounds/' . $rounds->first()->id . '/fetch');

        $players->each(function ($player) {
            $this->see('{"id":' . $player->id . ',"name":"'. $player->name .'","temp":0,"recent":0,"rounds":5,"round":{"exists":1,"best_player":0,"second_best_player":0,"quarters":{"count":0,"reason":""}}}');
        });

    }

    /** @test */
    public function user_can_destroy_association_between_team_and_round()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $round = factory(App\Round::class)->create();
        $players = factory(App\Player::class, 5)->create();

        $user->team()->save($team);
        $players->each(function ($player) use ($team) {
            $team->players()->attach($player);
        });
        $players->each(function ($player) use ($round) {
            $round->players()->attach($player);

            $this->seeInDatabase('player_round', [
                'round_id' => $round->id,
                'player_id' => $player->id,
            ]);
        });

        // initial store
        $this->actingAs($user)
            ->call('DELETE', '/teams/' . $team->slug . '/rounds/' . $round->id);

        $this->dontSeeInDatabase('round_team', [
                'round_id' => $round->id,
                'team_id' => $team->id,
                'date' => NULL,
            ]);

        $players->each(function ($player) use ($round) {
            $this->dontSeeInDatabase('player_round', [
                'round_id' => $round->id,
                'player_id' => $player->id,
            ]);
        });

    }

    /** @test */
    public function user_can_get_best_players_allowed_status()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();

        $user->team()->save($team);

        $response = $this->actingAs($user)
            ->call('GET', '/teams/' . $team->slug . '/bestPlayersAllowed/status');

        $this->assertTrue($response->content() === 'true');

        $team->update(['best_players_allowed' => 0]);

        $response = $this->actingAs($user)
            ->call('GET', '/teams/' . $team->slug . '/bestPlayersAllowed/status');

        $this->assertTrue($response->content() === 'false');
    }

    /**
     * Validation
     */

    /** @test */
    public function date_doesnt_get_inserted_if_date_is_submitted_for_round_and_is_not_correct_format()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id . '/date', [
                'date' => '2018-01-02'
            ]);

        $this->dontSeeInDatabase('round_team', [
            'date' => '2018-01-02',
        ]);

        $this->dontSeeInDatabase('round_team', [
            'date' => '2018-01-02 00:00:00',
        ]);
    }

    /** @test */
    public function date_doesnt_get_inserted_if_date_is_not_submitted_for_round()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id . '/date', [
                'date' => '2018-01-02',
            ]);

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id . '/date', [
                //
            ]);

        $this->seeInDatabase('round_team', [
            'round_id' => $rounds->first()->id,
            'team_id' => $team->id,
            'date' => Carbon\Carbon::parse('2018-01-02')->toDateTimeString(),
        ]);
    }

    /** @test */
    public function user_cannot_store_round_with_player_from_other_team()
    {
        $users = factory(App\User::class, 2)->create();
        $teams = factory(App\Team::class, 2)->create();
        $rounds = factory(App\Round::class, 5)->create();
        $players = factory(App\Player::class, 5)->create();

        $users->first()->team()->save($teams->first());
        $users->last()->team()->save($teams->last());

        $teams->first()->players()->attach($players->find(1));
        $teams->first()->players()->attach($players->find(2));
        $teams->first()->players()->attach($players->find(3));

        $teams->last()->players()->attach($players->find(4));
        $teams->last()->players()->attach($players->find(5));


        $this->actingAs($users->first())
            ->call('POST', '/teams/' . $teams->first()->slug . '/rounds/' . $rounds->first()->id);

        $this->actingAs($users->first())
            ->call('PUT', '/teams/' . $teams->first()->slug . '/rounds/' . $rounds->first()->id, [
                'players' =>
                    [
                        [
                            // player 4 belongs to team 2 (not this team)
                            'id' => $players->find(4)->id,
                            'round' => [
                                'best_player' => 0,
                                'second_best_player' => 0,
                                'quarters' => [
                                    'count' => 1,
                                    'reason' => NULL
                                ],
                            ],
                        ]
                    ]
                ]);

        $this->assertResponseStatus(404);

        $this->dontSeeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->find(4)->id
        ]);
    }

    /** @test */
    public function user_cant_store_round_with_no_data()
    {
        $users = factory(App\User::class, 2)->create();
        $teams = factory(App\Team::class, 2)->create();
        $rounds = factory(App\Round::class, 5)->create();
        $players = factory(App\Player::class, 5)->create();

        $users->first()->team()->save($teams->first());
        $users->last()->team()->save($teams->last());

        $teams->first()->players()->attach($players->first());

        $this->actingAs($users->first())
            ->call('POST', '/teams/' . $teams->first()->slug . '/rounds/' . $rounds->first()->id);

        $this->actingAs($users->first())
            ->call('PUT', '/teams/' . $teams->first()->slug . '/rounds/' . $rounds->first()->id, [
                'players' =>
                    [
                        [
                            'id' => $players->first()->id,
                            'round' => [
                                'best_player' => 0,
                                'second_best_player' => 0,
                                'quarters' => [
                                    'count' => 1,
                                    'reason' => NULL
                                ],
                            ],
                        ]
                    ]
                ]);

        $this->assertResponseStatus(200);

        $this->seeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->first()->id
        ]);

        $this->seeInDatabase('players', [
            'id' => $players->first()->id,
            'recent' => 1,
        ]);

        $this->actingAs($users->first())
            ->call('PUT', '/teams/' . $teams->first()->slug . '/rounds/' . $rounds->first()->id, [
                'players' =>
                    [
                        //
                    ]
                ]);

        $this->assertResponseStatus(400);

        $this->seeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->first()->id
        ]);

        $this->seeInDatabase('players', [
            'id' => $players->first()->id,
            'recent' => 1,
        ]);

        $this->actingAs($users->first())
            ->call('PUT', '/teams/' . $teams->first()->slug . '/rounds/' . $rounds->first()->id, [
                'players' =>
                    [
                        [
                            //
                        ]
                    ]
                ]);

        $this->assertResponseStatus(400);

        $this->seeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->first()->id
        ]);

        $this->seeInDatabase('players', [
            'id' => $players->first()->id,
            'recent' => 1,
        ]);

        $this->actingAs($users->first())
            ->call('PUT', '/teams/' . $teams->first()->slug . '/rounds/' . $rounds->first()->id, [
                    //
                ]);

        $this->assertResponseStatus(400);

        $this->seeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->first()->id
        ]);

        $this->seeInDatabase('players', [
            'id' => $players->first()->id,
            'recent' => 1,
        ]);
    }

    /** @test */
    public function user_cant_store_round_with_player_that_doesnt_exist()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->actingAs($user)
            ->call('PUT', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id, [
                'players' =>
                    [
                        [
                            // player 999 doesnt exist
                            'id' => 999,
                            'round' => [
                                'best_player' => 0,
                                'second_best_player' => 0,
                                'quarters' => [
                                    'count' => 1,
                                    'reason' => NULL
                                ],
                            ],
                        ]
                    ]
                ]);

        $this->assertResponseStatus(404);

        $this->dontSeeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => 999
        ]);
    }

    /** @test */
    public function user_cant_store_round_with_invalid_quarters()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();
        $players = factory(App\Player::class, 5)->create();

        $user->team()->save($team);

        $players->each(function ($player) use (&$team) {
            $team->players()->attach($player);
        });

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        // quarter posted as string
        $response = $this->actingAs($user)
            ->call('PUT', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id, [
                'players' =>
                    [
                        [
                            'id' => $players->first()->id,
                            'round' => [
                                'best_player' => 0,
                                'second_best_player' => 0,
                                'quarters' => [
                                    'count' => 'hello',
                                    'reason' => NULL
                                ],
                            ],
                        ]
                    ]
            ]);

        $this->assertTrue($response->getContent() == '{"player_id":'. $players->first()->id . '}');

        $this->assertResponseStatus(422);

        $this->dontSeeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->first()->id
        ]);

        // quarters posted as invalid integer
        $response = $this->actingAs($user)
            ->call('PUT', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id, [
                'players' =>
                    [
                        [
                            'id' => $players->first()->id,
                            'round' => [
                                'best_player' => 0,
                                'second_best_player' => 0,
                                'quarters' => [
                                    'count' => 99,
                                    'reason' => NULL
                                ],
                            ],
                        ]
                    ]
            ]);

        $this->assertTrue($response->getContent() == '{"player_id":'. $players->first()->id . '}');

        $this->assertResponseStatus(422);

        $this->dontSeeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->first()->id
        ]);
    }

    /** @test */
    public function existing_player_recent_flag_and_player_round_assoc_doesnt_change_if_round_submission_was_invalid()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();
        $players = factory(App\Player::class, 5)->create();

        $user->team()->save($team);

        $players->each(function ($player) use (&$team) {
            $team->players()->attach($player);
        });

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        // valid submission
        $this->actingAs($user)
            ->call('PUT', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id, [
                'players' =>
                    [
                        [
                            'id' => $players->first()->id,
                            'round' => [
                                'best_player' => 0,
                                'second_best_player' => 0,
                                'quarters' => [
                                    'count' => 2,
                                    'reason' => NULL
                                ],
                            ],
                        ]
                    ]
            ]);

        $this->assertResponseStatus(200);

        $this->seeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->first()->id
        ]);

        $this->seeInDatabase('players', [
            'id' => $players->first()->id,
            'recent' => 1,
        ]);

        // invalid submission
        $response = $this->actingAs($user)
            ->call('PUT', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id, [
                'players' =>
                    [
                        [
                            'id' => $players->last()->id,
                            'round' => [
                                'best_player' => 0,
                                'second_best_player' => 0,
                                'quarters' => [
                                    'count' => 99,
                                    'reason' => NULL
                                ],
                            ],
                        ]
                    ]
            ]);

        $this->assertTrue($response->getContent() == '{"player_id":'. $players->last()->id . '}');

        $this->assertResponseStatus(422);

        $this->dontSeeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->last()->id
        ]);

        $this->dontSeeInDatabase('players', [
            'id' => $players->last()->id,
            'recent' => 1,
        ]);

        $this->seeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->first()->id
        ]);

        $this->seeInDatabase('players', [
            'id' => $players->first()->id,
            'recent' => 1,
        ]);
    }

    /** @test */
    public function old_players_are_unflagged_as_recent_and_removed_from_current_round_assoc()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();
        $players = factory(App\Player::class, 5)->create();

        $user->team()->save($team);

        $players->each(function ($player) use (&$team) {
            $team->players()->attach($player);
        });

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        // submit initial data
        $this->actingAs($user)
            ->call('PUT', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id, [
                'players' =>
                    [
                        [
                            'id' => $players->first()->id,
                            'round' => [
                                'best_player' => 0,
                                'second_best_player' => 0,
                                'quarters' => [
                                    'count' => 2,
                                    'reason' => NULL
                                ],
                            ],
                        ]
                    ]
            ]);

        $this->assertResponseStatus(200);

        $this->seeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->first()->id,
            'quarters' => 2,
            'quarters_reason' => NULL
        ]);

        $this->seeInDatabase('players', [
            'id' => $rounds->first()->id,
            'recent' => 1,
        ]);

        // update data
        $this->actingAs($user)
            ->call('PUT', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id, [
                'players' =>
                    [
                        [
                            'id' => $players->last()->id,
                            'round' => [
                                'best_player' => 0,
                                'second_best_player' => 0,
                                'quarters' => [
                                    'count' => 1,
                                    'reason' => NULL
                                ],
                            ],
                        ]
                    ]
            ]);

        $this->assertResponseStatus(200);

        // check old data was dealt with
        $this->dontSeeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->first()->id
        ]);

        $this->seeInDatabase('players', [
            'id' => $rounds->first()->id,
            'recent' => 0,
        ]);

        $this->seeInDatabase('players', [
            'id' => $rounds->last()->id,
            'recent' => 1,
        ]);

        $this->seeInDatabase('player_round', [
            'round_id' => $rounds->first()->id,
            'player_id' => $players->last()->id,
            'quarters' => 1,
            'quarters_reason' => NULL
        ]);
    }

    /**
     * Authorization
     */

    /** @test */
    public function user_can_access_their_own_rounds_index_view()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->call('GET', '/teams/' . $team->slug . '/rounds');

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function other_user_cannot_access_someone_elses_rounds_index()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $users->first()->team()->save($team);

        $this->actingAs($users->last())
            ->call('GET', '/teams/' . $team->slug . '/rounds');

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function admin_user_is_able_to_access_another_users_rounds_index()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $users->last()->update([
            'role' => 'admin',
        ]);

        $users->first()->team()->save($team);

        $this->actingAs($users->last())
            ->call('GET', '/teams/' . $team->slug . '/rounds');

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function user_can_access_their_own_rounds_entry_view()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->call('GET', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function other_user_cannot_access_someone_elses_rounds_entry_view()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $users->first()->team()->save($team);

        $this->actingAs($users->last())
            ->call('GET', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function admin_user_is_able_to_access_another_users_rounds_entry_view()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $users->last()->update([
            'role' => 'admin',
        ]);

        $users->first()->team()->save($team);

        $this->actingAs($users->last())
            ->call('GET', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function user_can_store_date_for_a_round_under_their_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id . '/date', [
                'date' => '2018-01-03',
            ]);

        $this->seeInDatabase('round_team', [
            'team_id' => $team->id,
            'round_id' => $rounds->first()->id,
            'date' => Carbon\Carbon::parse('2018-01-03')->toDateTimeString(),
        ]);
    }

    /** @test */
    public function other_user_cant_store_date_for_a_round_under_another_team()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $users->first()->team()->save($team);

        $this->actingAs($users->last())
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id . '/date', [
                'date' => '2018-01-02 00:00:00',
            ]);

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function admin_user_is_able_to_store_date_for_a_round_under_another_team()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $users->first()->team()->save($team);
        $users->last()->update([
            'role' => 'admin',
        ]);

        $this->actingAs($users->last())
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);
        $this->actingAs($users->last())
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id . '/date', [
                'date' => '2018-01-04',
            ]);

        $this->seeInDatabase('round_team', [
            'team_id' => $team->id,
            'round_id' => $rounds->first()->id,
            'date' => Carbon\Carbon::parse('2018-01-04')->toDateTimeString(),
        ]);
    }

    /** @test */
    public function user_can_store_update_round_input_for_their_own_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();
        $players = factory(App\Player::class, 5)->create();

        $user->team()->save($team);

        $players->each(function ($player) use (&$team) {
            $team->players()->attach($player);
        });

        $this->actingAs($user)
            ->call('GET', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->actingAs($user)
            ->call('PUT', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id, [
            'players' =>
                [
                    [
                        'id' => $players->first()->id,
                        'round' => [
                            'best_player' => 0,
                            'second_best_player' => 0,
                            'quarters' => [
                                'count' => 1,
                                'reason' => NULL
                            ],
                        ],
                    ]
                ]
            ]);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function other_user_cant_store_update_round_input_for_a_team_they_dont_own()
    {
        $users = factory(App\User::class, 2)->create();
        $teams = factory(App\Team::class, 2)->create();
        $rounds = factory(App\Round::class, 5)->create();
        $players = factory(App\Player::class, 5)->create();

        $users->first()->team()->save($teams->first());
        $users->last()->team()->save($teams->last());

        $players->each(function ($player) use (&$teams) {
            $teams->first()->players()->attach($player);
        });

        $this->actingAs($users->last())
            ->call('GET', '/teams/' . $teams->first()->slug . '/rounds/' . $rounds->first()->id);

        $this->assertResponseStatus(403);

        $this->actingAs($users->last())
            ->call('PUT', '/teams/' . $teams->first()->slug . '/rounds/' . $rounds->first()->id, [
                'players' =>
                    [
                        [
                            'id' => $players->first()->id,
                            'round' => [
                                'best_player' => 0,
                                'second_best_player' => 0,
                                'quarters' => 1,
                            ],
                        ]
                    ]
                ]);

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function admin_user_can_store_update_round_input_for_a_team_they_dont_own()
    {
        $users = factory(App\User::class, 2)->create();
        $teams = factory(App\Team::class, 2)->create();
        $rounds = factory(App\Round::class, 5)->create();
        $players = factory(App\Player::class, 5)->create();

        $users->first()->team()->save($teams->first());
        $users->last()->team()->save($teams->last());

        $users->last()->update(['role' => 'admin']);

        $players->each(function ($player) use (&$teams) {
            $teams->first()->players()->attach($player);
        });

        $this->actingAs($users->last())
            ->call('GET', '/teams/' . $teams->first()->slug . '/rounds/' . $rounds->first()->id);

        $this->actingAs($users->last())
            ->call('PUT', '/teams/' . $teams->first()->slug . '/rounds/' . $rounds->first()->id, [
            'players' =>
                [
                    [
                        'id' => $players->first()->id,
                        'round' => [
                            'best_player' => 0,
                            'second_best_player' => 0,
                            'quarters' => [
                                'count' => 1,
                                'reason' => NULL
                            ],
                        ],
                    ]
                ]
            ]);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function user_can_save_association_between_their_team_and_a_round()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->assertRedirectedTo('/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);
    }

    /** @test */
    public function other_user_cant_save_association_between_a_team_they_dont_own_and_a_round()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $users->first()->team()->save($team);

        $this->actingAs($users->last())
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function admin_can_save_association_between_a_team_they_dont_own_and_a_round()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $users->first()->update(['role' => 'admin']);

        $users->last()->team()->save($team);

        $this->actingAs($users->first())
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->assertRedirectedTo('/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);
    }

    /** @test */
    public function user_can_destroy_association_between_their_team_and_a_round()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->seeInDatabase('round_team', [
            'round_id' => $rounds->first()->id,
            'team_id' => $team->id,
        ]);

        $this->actingAs($user)
            ->call('DELETE', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->dontSeeInDatabase('round_team', [
            'round_id' => $rounds->first()->id,
            'team_id' => $team->id,
        ]);
    }

    /** @test */
    public function other_user_cant_destroy_association_between_a_team_they_dont_own_and_a_round()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $users->first()->team()->save($team);

        // attaching team/ round as first user
        $this->actingAs($users->first())
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->seeInDatabase('round_team', [
            'round_id' => $rounds->first()->id,
            'team_id' => $team->id,
        ]);

        // detaching team/ round as second user
        $this->actingAs($users->last())
            ->call('DELETE', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        // no changes made
        $this->seeInDatabase('round_team', [
            'round_id' => $rounds->first()->id,
            'team_id' => $team->id,
        ]);

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function admin_can_destroy_association_between_a_team_they_dont_own_and_a_round()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $users->last()->update(['role' => 'admin']);

        $users->first()->team()->save($team);

        // acting as first, non admin user
        $this->actingAs($users->first())
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->seeInDatabase('round_team', [
            'round_id' => $rounds->first()->id,
            'team_id' => $team->id,
        ]);

        // acting as admin user
        $this->actingAs($users->last())
            ->call('DELETE', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->dontSeeInDatabase('round_team', [
            'round_id' => $rounds->first()->id,
            'team_id' => $team->id,
        ]);
    }

    /** @test */
    public function other_user_cannot_get_best_players_allowed_status_for_a_team_they_dont_own()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();

        $users->first()->team()->save($team);

        $this->actingAs($users->last())
            ->call('GET', '/teams/' . $team->slug . '/bestPlayersAllowed/status');

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function admin_user_can_get_best_players_allowed_status_for_a_team_they_dont_own()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();

        $users->last()->update(['role' => 'admin']);
        $users->first()->team()->save($team);

        $this->actingAs($users->last())
            ->call('GET', '/teams/' . $team->slug . '/bestPlayersAllowed/status');

        $this->assertResponseStatus(200);
    }
}
