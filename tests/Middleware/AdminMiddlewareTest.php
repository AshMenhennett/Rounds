<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminMiddlewareTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function admin_middleware_prevents_non_admin_from_accessing_admin_area()
    {
        $user = factory(App\User::class)->create();

        $this->actingAs($user)
            ->visit('/admin');

        $this->seePageIs('/home');
    }
}
