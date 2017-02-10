<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CoachTeamTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_stays_on_correct_url_when_they_have_no_team()
    {
        $user = factory(App\User::class)->create();

        $this->actingAs($user)
            ->visit('/teams')
            ->seePageIs('/teams');
    }

    /** @test */
    public function user_is_redirected_to_correct_url_when_they_have_a_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->visit('/teams')
            ->seePageIs('/home');
    }

    /** @test */
    public function user_sees_error_when_there_are_no_available_teams_when_logged_in_and_doesnt_have_a_team()
    {
        $user = factory(App\User::class)->create();

        $this->actingAs($user)
            ->visit('/teams')
            ->see('There are currently no available teams.');
    }

    /** @test */
    public function user_sees_available_teams_when_logged_in_and_doesnt_have_a_team()
    {
        $user = factory(App\User::class)->create();
        $teams = factory(App\Team::class, 5)->create();

        $this->actingAs($user)
            ->visit('/teams');

        $teams->each(function ($team) {
            $this->see($team->name);
        });
    }

    /** @test */
    public function user_is_able_to_join_team()
    {
        $user = factory(App\User::class)->create();
        $teams = factory(App\Team::class, 5)->create();

        $this->actingAs($user)
            ->call('POST', '/teams/' . $teams->first()->slug);

        $this->seeInDatabase('teams', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function user_is_able_to_join_only_one_team()
    {
        $user = factory(App\User::class)->create();
        $teams = factory(App\Team::class, 5)->create();

        $user->team()->save($teams->first());

        $this->actingAs($user)
            ->call('POST', '/teams/' . $teams->last()->slug);

        $this->dontSeeInDatabase('teams', [
            'id' => $teams->last()->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function user_can_click_on_manage_link_and_be_taken_to_team_management_view()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->visit('/home')
            ->click('Manage');

        $this->seePageIs('/teams/' . $team->slug . '/manage');
    }

    /** @test */
    public function user_can_click_on_add_players_link_in_team_management_view_and_be_taken_to_correct_view()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->visit('/teams/' . $team->slug . '/manage')
            ->click('Add Players');

        $this->seePageIs('/teams/' . $team->slug . '/players');
    }

    /** @test */
    public function user_can_click_on_fill_in_round_link_in_team_management_view_and_be_taken_to_correct_view()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->visit('/teams/' . $team->slug . '/manage')
            ->click('Fill in Round');

        $this->seePageIs('/teams/' . $team->slug . '/rounds');
    }

    /** @test */
    public function user_can_press_leave_team_button_in_team_management_view_and_be_taken_to_correct_view_as_well_as_being_removed_from_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();

        $user->team()->save($team);

        $this->seeInDatabase('teams', [
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->visit('/teams/' . $team->slug . '/manage')
            ->press('Leave Team');

        $this->dontSeeInDatabase('teams', [
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function user_can_see_stats_in_team_management_view()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $players = factory(App\Player::class, 5)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $user->team()->save($team);

        $players->each(function ($player) use (&$team) {
            $team->players()->attach($player);
        });
        $rounds->each(function ($round) use (&$team) {
            $team->rounds()->attach($round);
        });

        $this->actingAs($user)
            ->visit('/teams/' . $team->slug . '/manage')
            ->see('Stats for ' . strtoupper($team->name))
            ->see('Coach: <strong>' . $team->user->name() . '</strong>.')
            ->see(strtoupper($team->name) . ' has played in <strong>' . count($team->rounds) . '</strong> rounds.')
            ->see(strtoupper($team->name) . ' has <strong>' . count($team->players) . '</strong> players.');
    }

    /**
     * Authorization
     */

    /** @test */
    public function user_can_access_their_own_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->call('GET', '/teams/' . $team->slug . '/manage');

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function other_user_cannot_access_someone_elses_team()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();

        $users->first()->team()->save($team);

        $this->actingAs($users->last())
            ->call('GET', '/teams/' . $team->slug . '/manage');

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function admin_user_is_able_to_access_another_users_team()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();

        $users->last()->update([
            'role' => 'admin',
        ]);

        $users->first()->team()->save($team);

        $this->actingAs($users->last())
            ->call('GET', '/teams/' . $team->slug . '/manage');

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function user_is_able_to_leave_team()
    {
        $user = factory(App\User::class)->create();
        $teams = factory(App\Team::class, 5)->create();

        $user->team()->save($teams->first());

        $this->seeInDatabase('teams', [
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->call('DELETE', '/teams/' . $teams->first()->slug . '/coach');

        $this->dontSeeInDatabase('teams', [
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function user_is_not_able_to_leave_team_if_they_dont_own_it()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();

        $users->first()->team()->save($team);

        $this->actingAs($users->last())
            ->call('DELETE', '/teams/' . $team->slug . '/coach');

        $this->seeInDatabase('teams', [
            'user_id' => $users->first()->id,
        ]);
    }

    /** @test */
    public function admin_user_is_not_able_to_leave_team_if_they_dont_own_it()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();

        $users->last()->update([
            'role' => 'admin',
        ]);

        $users->first()->team()->save($team);

        $this->actingAs($users->last())
            ->call('DELETE', '/teams/' . $team->slug . '/coach');

        $this->seeInDatabase('teams', [
            'user_id' => $users->first()->id,
        ]);
    }

    /** @test */
    public function user_can_see_add_players_button_and_leave_group_button_for_their_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->visit('/teams/' . $team->slug . '/manage')
            ->see('Leave Team')
            ->see('Add Players');
    }

}
