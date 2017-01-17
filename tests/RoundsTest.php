<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RoundsTest extends TestCase
{
    use DatabaseTransactions;

    // test fetchPlayers Round

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
    public function user_can_see_view_to_fill_in_round_data()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $round = factory(App\Round::class)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->visit('/teams/' . $team->slug . '/rounds/' . $round->id);

            // elaborate
            $this->assertTrue(false);

        $this->see('Fill in Round ' . $round->name);
    }

    /** @test */
    public function user_gets_attached_to_round_on_round_show()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $round = factory(App\Round::class)->create();

        $user->team()->save($team);

       $this->actingAs($user)
            ->visit('/teams/' . $team->slug . '/rounds/' . $round->id);

        $this->seeInDatabase('round_team', [
            'round_id' => $round->id,
            'team_id' => $team->id,
        ]);
    }

    /** @test */
    public function user_can_only_attach_themself_to_a_round_once()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->call('GET', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->seeInDatabase('round_team', [
            'id' => 1,
            'round_id' => $rounds->first()->id,
            'team_id' => $team->id,
        ]);

        $this->actingAs($user)
            ->call('GET', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->dontSeeInDatabase('round_team', [
            'id' => 2,
            'round_id' => $rounds->first()->id,
            'team_id' => $team->id,
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
            $this->see('{"id":' . $player->id . ',"name":"'. $player->name .'","temp":0,"recent":0,"rounds":5,"round":{"exists":1,"best_player":0,"second_best_player":0,"quarters":0}}');
        });

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
            ->visit('/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id . '/date', [
                'date' => '2018-01-02 00:00:00',
            ]);

        $this->actingAs($user)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id . '/date', [
                //
            ]);

        $this->seeInDatabase('round_team', [
            'round_id' => $rounds->first()->id,
            'team_id' => $team->id,
            'date' => '2018-01-02 00:00:00',
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
            ->visit('/teams/' . $team->slug . '/rounds/' . $rounds->first()->id)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id . '/date', [
                'date' => '2018-01-03 00:00:00',
            ]);

        $this->seeInDatabase('round_team', [
            'team_id' => $team->id,
            'round_id' => $rounds->first()->id,
            'date' => '2018-01-03 00:00:00',
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
            ->visit('/teams/' . $team->slug . '/rounds/' . $rounds->first()->id)
            ->call('POST', '/teams/' . $team->slug . '/rounds/' . $rounds->first()->id . '/date', [
                'date' => '2018-01-04 00:00:00',
            ]);

        $this->seeInDatabase('round_team', [
            'team_id' => $team->id,
            'round_id' => $rounds->first()->id,
            'date' => '2018-01-04 00:00:00',
        ]);
    }

}
