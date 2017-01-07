<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PlayerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_can_access_players_as_array_in_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();

        $user->team()->save($team);

        $this->actingAs($user)
            ->visit('/teams/' . $team->slug . '/players/fetch');

        $this->assertResponseStatus(200);
    }

    // /** @test */
    // public function user_sees_player_error_when_no_players_in_team()
    // {
    //     $user = factory(App\User::class)->create();
    //     $team = factory(App\Team::class)->create();

    //     $user->team()->save($team);

    //     $this->actingAs($user)
    //         ->visit('/teams/' . $team->slug . '/players');

    //     $this->see('There are currently no players for this team.')
    //         ->see('There are currently no temporary players for this team.');
    // }

    /** @test */
    public function user_can_edit_and_update_player_name_and_temp_in_team()
    {
        $user = factory(App\User::class)->create();
        $team = factory(App\Team::class)->create();
        $player = factory(App\Player::class)->create();

        $user->team()->save($team);
        $team->players()->attach($player);

        $this->actingAs($user);

        $this->visit('/teams/' . $team->slug . '/players/' . $player->id . '/edit')
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

        $user->team()->save($team);
        $team->players()->attach($player);
        $player->rounds()->attach($round);
        $round->teams()->attach($team);

        $this->actingAs($user)
            ->call('DELETE', '/teams/' . $team->slug . '/players/' . $player->id);

        $this->assertResponseStatus(400);
    }

    // add player
    // test authoirzation

}
