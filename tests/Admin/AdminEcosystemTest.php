<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminEcosystemTest extends BrowserKitTestCase
{
    /** @test */
    public function admin_can_see_ecosystem_buttons_form()
    {
        $admin = factory(App\User::class)->create(['role' => 'admin']);

        $url = config('app.url');

        $this->actingAs($admin)
            ->visit('/admin/ecosystem/buttons');

        $this->see('Create some buttons to show on the <a href="'.$url.'/ecosystem">ecosystem</a> page.');
    }

    /** @test */
    public function admin_can_create_a_link_button()
    {
        $admin = factory(App\User::class)->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->visit('/admin/ecosystem/buttons')
            ->type('My Link Button Text', 'value')
            ->type('http://google.com', 'link')
            ->press('Add Button')
            ->seePageIs('/admin/ecosystem/buttons');

        $this->assertResponseStatus(200);

        $this->seeInDatabase('ecosystem_buttons', [
            'value' => 'My Link Button Text',
            'link' => 'http://google.com',
            'file_name' => null,
         ]);
    }

    /** @test */
    public function admin_can_create_a_file_button()
    {
        $admin = factory(App\User::class)->create(['role' => 'admin']);
        $url = config('app.url');
        copy(storage_path('test_files/master_files/Empty.xlsx'), storage_path('test_files/Test.xlsx'));

        $this->actingAs($admin)
            ->visit('/admin/ecosystem/buttons')
            ->type('My File Button Text', 'value')
            ->attach(storage_path('test_files/Test.xlsx'), 'file')
            ->press('Add Button')
            ->seePageIs('/admin/ecosystem/buttons');

        $this->assertResponseStatus(200);

        $button = \App\EcosystemButton::find(1);

        $this->seeInDatabase('ecosystem_buttons', [
            'value' => 'My File Button Text',
            'link' => null,
            'file_name' => $button->file_name,
         ]);
    }

    /** @test */
    public function admin_can_delete_an_ecosystem_button()
    {
        $admin = factory(App\User::class)->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->visit('/admin/ecosystem/buttons')
            ->type('My File Button Text', 'value')
            ->type('http://google.com', 'link')
            ->press('Add Button')
            ->seePageIs('/admin/ecosystem/buttons');

        $this->assertResponseStatus(200);

        $this->seeInDatabase('ecosystem_buttons', [
            'value' => 'My File Button Text',
            'link' => 'http://google.com',
            'file_name' => null,
         ]);

        $this->actingAs($admin)
            ->call('DELETE', '/admin/ecosystem/buttons/' . 1);

        $this->assertResponseStatus(200);

        $this->dontSeeInDatabase('ecosystem_buttons', [
            'value' => 'My File Button Text',
            'link' => 'http://google.com',
            'file_name' => null,
         ]);
    }

    /**
     * Validation
     */

    /** @test */
    public function admin_gets_validation_error_when_they_dont_provide_any_values_for_the_button()
    {
        $admin = factory(App\User::class)->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->call('POST', '/admin/ecosystem/buttons/new', []);

        $this->assertResponseStatus(302);
    }

    /** @test */
    public function admin_gets_validation_error_when_they_dont_provide_a_value_for_the_button()
    {
        $admin = factory(App\User::class)->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->call('POST', '/admin/ecosystem/buttons/new', [
                    'link' => 'http://google.com'
                ]);

        $this->assertResponseStatus(302);

        $this->dontSeeInDatabase('ecosystem_buttons', [
            'link' => 'http://google.com',
         ]);
    }

    /** @test */
    public function admin_gets_validation_error_when_they_dont_provide_a_link_or_a_file_for_the_button()
    {
        $admin = factory(App\User::class)->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->call('POST', '/admin/ecosystem/buttons/new', [
                    'value' => 'My Cool Button'
                ]);

        $this->assertResponseStatus(302);

        $this->dontSeeInDatabase('ecosystem_buttons', [
            'value' => 'My Cool Button',
         ]);
    }
}
