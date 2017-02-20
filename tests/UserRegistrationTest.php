<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function guest_sees_SMAA_Rounds_at_base_route()
    {
        $this->visit('/')
             ->see('SMAA Rounds');
    }

    /** @test */
    public function guest_registers_successfully_and_sees_name_once_logged_in()
    {
        $this->visit('/register')
            ->type('Ashley', 'first_name')
            ->type('Menhennett', 'last_name')
            ->type('ashleymenhennett@gmail.com', 'email')
            ->type('secret', 'password')
            ->type('secret', 'password_confirmation')
            ->press('Register')
            ->seePageIs('/home')
            ->see('Ashley Menhennett')
            ->see('Find a Team');
    }

}
