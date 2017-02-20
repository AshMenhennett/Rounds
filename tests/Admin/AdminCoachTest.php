<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminCoachTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function admin_can_fetch_paginated_coaches()
    {
        $admin = factory(App\User::class)->create(['role' => 'admin']);
        $coaches = factory(App\User::class, 5)->create(['role' => 'coach']);

        $this->actingAs($admin)
            ->call('GET', '/admin/coaches/fetch');

        $coaches->each(function ($coach) {
            $this->see('{"id":' . $coach->id . ',"first_name":"' . $coach->first_name . '","last_name":"' . $coach->last_name . '","email":"'. $coach->email .'","role":"coach","team":{"name":null,"slug":null}}');
        });

        $this->see('"meta":{"pagination":{"total":6,"count":6,"per_page":10,"current_page":1,"total_pages":1,"links":[]}}');
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
    public function admin_can_delete_coach()
    {
        $admin = factory(App\User::class)->create(['role' => 'admin']);
        $coach = factory(App\User::class)->create(['role' => 'coach']);

        $this->actingAs($admin)
            ->call('DELETE', '/admin/coaches/' . $coach->id);

        $this->assertResponseStatus(200);

        $this->dontSeeInDatabase('users', [
                'id' => $coach->id,
                'first_name' => $coach->first_name,
                'last_name' => $coach->last_name,
            ]);
    }

    /** @test */
    public function admin_cannot_delete_coach_if_coach_is_actually_an_admin()
    {
        $admin = factory(App\User::class)->create(['role' => 'admin']);
        $admin2 = factory(App\User::class)->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->call('DELETE', '/admin/coaches/' . $admin2->id);

        $this->assertResponseStatus(403);

        $this->seeInDatabase('users', [
                'id' => $admin2->id,
                'first_name' => $admin2->first_name,
                'last_name' => $admin2->last_name,
            ]);
    }

}
