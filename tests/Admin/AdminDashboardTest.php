<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminDashboardTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function admin_sees_admin_dashboard()
    {
        $user = factory(App\User::class, 1)->create(['role' => 'admin']);

        $this->actingAs($user)
            ->visit('/admin');

        $this->see('Admin Dashboard');
    }

    /** @test */
    public function coach_cannot_access_admin_dashboard()
    {
        $user = factory(App\User::class, 1)->create(['role' => 'coach']);

        $this->actingAs($user)
            ->visit('/admin');

        // redirected to home
        $this->seePageIs('/home');
    }
}
