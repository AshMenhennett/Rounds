<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TeamTest extends TestCase
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
            ->call('post', '/teams/' . $teams->first()->slug);

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
            ->call('post', '/teams/' . $teams->last()->slug);

        $this->dontSeeInDatabase('teams', [
            'id' => $teams->last()->id,
            'user_id' => $user->id,
        ]);
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
            ->call('GET', '/teams/' . $team->slug);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function other_user_cannot_access_someone_elses_team()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();

        $users->first()->team()->save($team);

        $this->actingAs($users->last())
            ->call('GET', '/teams/' . $team->slug);

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
            ->call('GET', '/teams/' . $team->slug);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function user_is_able_to_leave_team()
    {
        $user = factory(App\User::class)->create();
        $teams = factory(App\Team::class, 5)->create();

        $user->team()->save($teams->first());

        $this->actingAs($user)
            ->call('delete', '/teams/' . $teams->first()->slug . '/coach');

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

}
