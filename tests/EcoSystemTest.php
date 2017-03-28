<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EcoSystemTest extends BrowserKitTestCase
{

    /** @test */
    public function user_can_see_all_ecosystem_buttons()
    {
        $admin = factory(App\User::class)->create(['role' => 'admin']);
        $user = factory(App\User::class)->create();

        $this->actingAs($admin)
            ->visit('/admin/ecosystem/buttons')
            ->type('My Link Button Text', 'value')
            ->type('http://google.com', 'link')
            ->press('Add to Ecosystem')
            ->seePageIs('/admin/ecosystem/buttons');

        $this->actingAs($admin)
            ->visit('/admin/ecosystem/buttons')
            ->type('My Link Button Text 2', 'value')
            ->type('http://google.com', 'link')
            ->press('Add to Ecosystem')
            ->seePageIs('/admin/ecosystem/buttons');

        $this->actingAs($admin)
            ->visit('/admin/ecosystem/buttons')
            ->type('My Link Button Text 3', 'value')
            ->type('http://google.com', 'link')
            ->press('Add to Ecosystem')
            ->seePageIs('/admin/ecosystem/buttons');

        $this->assertResponseStatus(200);

        $this->seeInDatabase('ecosystem_buttons', [
            'value' => 'My Link Button Text',
            'link' => 'http://google.com',
            'file_name' => null,
         ]);

        $this->seeInDatabase('ecosystem_buttons', [
            'value' => 'My Link Button Text 2',
            'link' => 'http://google.com',
            'file_name' => null,
         ]);

        $this->seeInDatabase('ecosystem_buttons', [
            'value' => 'My Link Button Text 3',
            'link' => 'http://google.com',
            'file_name' => null,
         ]);


        $this->actingAs($user)
            ->visit('/ecosystem');

        $this->see('My Link Button Text');
        $this->see('My Link Button Text 2');
        $this->see('My Link Button Text 3');
    }
}
