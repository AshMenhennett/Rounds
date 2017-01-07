<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RoundTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_can_click_on_round_link_and_be_taken_to_round_view()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->visit('/teams/' . $team->slug)
            ->click('Round ' . $rounds->first()->name);

        $this->seePageIs('/teams/' . $team->slug . '/rounds/' . $rounds->first()->id);
    }

    /**
     * Authorization
     */

    /** @test */
    public function user_can_see_available_rounds_and_leave_group_button_for_their_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->visit('/teams/' . $team->slug)
            ->see('Leave Team');

        $rounds->each(function($round) {
            $this->see('Round ' . $round->name);
        });
    }

    /** @test */
    public function admin_user_can_see_available_rounds_and_leave_group_button_for_any_team()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $users->first()->update(['role' => 'admin']);

        $users->last()->team()->save($team);

        $this->actingAs($users->first())
            ->visit('/teams/' . $team->slug)
            ->see('Leave Team');

        $rounds->each(function($round) {
            $this->see('Round ' . $round->name);
        });
    }

    /** @test */
    public function other_user_cannot_see_available_rounds_and_leave_group_button_when_they_dont_own_the_team()
    {
        $users = factory(App\User::class, 2)->create();
        $team = factory(App\Team::class)->create();
        $rounds = factory(App\Round::class, 5)->create();

        $users->first()->team()->save($team);

        $this->actingAs($users->last())
            ->call('GET', '/teams/' . $team->slug);

        $this->assertResponseStatus(403);
    }

    // test model assoc
    // validation

}
