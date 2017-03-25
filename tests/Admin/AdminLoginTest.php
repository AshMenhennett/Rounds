<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminLoginTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function admin_gets_redirected_to_admin_home_on_login()
    {
        $user = factory(App\User::class)->create(['email' => 'bob@example.com', 'password' => bcrypt('secret'), 'role' => 'admin']);

        $this->visit('/login')
            ->type('bob@example.com', 'email')
            ->type('secret', 'password')
            ->press('Login')
            ->seePageIs('/admin');
    }
}
