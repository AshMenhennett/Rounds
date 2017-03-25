<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminDeleteAllTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function admin_can_clear_out_select_tables_except_admins()
    {
        $coaches = factory(App\User::class, 2)->create(['role' => 'coach']);
        $admins = factory(App\User::class, 2)->create(['role' => 'admin']);
        $teamsLotA = factory(App\Team::class, 2)->create();
        $teamsLotB = factory(App\Team::class, 2)->create();
        $playersLotA = factory(App\Player::class, 10)->create();
        $playersLotB = factory(App\Player::class, 10)->create();
        $playersLotC = factory(App\Player::class, 10)->create();
        $playersLotD = factory(App\Player::class, 10)->create();
        $rounds = factory(App\Round::class, 20)->create();

        // assign coaches and admins to teams
        $coaches->first()->team()->save($teamsLotA->first());
        $coaches->last()->team()->save($teamsLotA->last());

        $admins->first()->team()->save($teamsLotB->first());
        $admins->last()->team()->save($teamsLotB->last());

        // assign players to teams
        $playersLotA->each(function ($player) use ($teamsLotA) {
            $teamsLotA->first()->players()->save($player);
        });
        $playersLotB->each(function ($player) use ($teamsLotA) {
            $teamsLotA->last()->players()->save($player);
        });
        $playersLotC->each(function ($player) use ($teamsLotB) {
            $teamsLotB->first()->players()->save($player);
        });
        $playersLotD->each(function ($player) use ($teamsLotB) {
            $teamsLotB->last()->players()->save($player);
        });

        // assign teams to rounds
        $rounds->each(function ($round) use ($teamsLotA, $teamsLotB) {
            $teamsLotA->first()->rounds()->attach($round);
            $teamsLotA->last()->rounds()->attach($round);
            $teamsLotB->first()->rounds()->attach($round);
            $teamsLotB->last()->rounds()->attach($round);
        });

        // assign players to rounds
        $rounds->each(function ($round) use ($playersLotA, $playersLotB, $playersLotC, $playersLotD) {
            $playersLotA->each(function ($player) use ($round) {
                $round->players()->attach($player);
            });
            $playersLotB->each(function ($player) use ($round) {
                $round->players()->attach($player);
            });
            $playersLotC->each(function ($player) use ($round) {
                $round->players()->attach($player);
            });
            $playersLotD->each(function ($player) use ($round) {
                $round->players()->attach($player);
            });
        });

        $this->actingAs($admins->first())
            ->call('DELETE', '/admin/delete');

        $this->assertResponseStatus(200);

        $this->assertTrue(\App\User::all()->count() == 2);
        $this->assertTrue(\App\Team::all()->count() == 0);
        $this->assertTrue(\App\Player::all()->count() == 0);
        $this->assertTrue(\App\Round::all()->count() == 0);

    }
}
