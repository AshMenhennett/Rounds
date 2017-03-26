<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ValidationTest extends BrowserKitTestCase
{

    /** @test */
    public function valid_team_passes_team_exists_rule()
    {
        $team = factory(App\Team::class)->create();

        $rules = ['team' => 'team_exists'];
        $data = ['team' => $team->id];

        $v = $this->app['validator']->make($data, $rules);
        $this->assertTrue($v->passes());
    }

    /** @test */
    public function invalid_team_fails_team_exists_rule()
    {
        $team = factory(App\Team::class)->create();

        $rules = ['team' => 'team_exists'];
        $data = ['team' => 123];

        $v = $this->app['validator']->make($data, $rules);
        $this->assertTrue(! $v->passes());
    }

    /** @test */
    public function valid_team_passes_team_exists_by_slug_rule()
    {
        $team = factory(App\Team::class)->create();

        $rules = ['team' => 'team_exists_by_slug'];
        $data = ['team' => $team->slug];

        $v = $this->app['validator']->make($data, $rules);
        $this->assertTrue($v->passes());
    }

    /** @test */
    public function invalid_team_fails_team_exists_by_slug_rule()
    {
        $team = factory(App\Team::class)->create();

        $rules = ['team' => 'team_exists_by_slug'];
        $data = ['team' => 'slug'];

        $v = $this->app['validator']->make($data, $rules);
        $this->assertTrue(! $v->passes());
    }

    /** @test */
    public function unique_slug_passes_unique_slug_rule()
    {
        $team = factory(App\Team::class)->create(['name' => 'The Bears']);

        $rules = ['team_name' => 'unique_slug'];
        $data = ['team_name' => 'uniquenameuniqueslug'];

        $v = $this->app['validator']->make($data, $rules);
        $this->assertTrue($v->passes());
    }

    /** @test */
    public function non_unique_slug_fails_unique_slug_rule()
    {
        $team = factory(App\Team::class)->create(['name' => 'The Bears']);

        $rules = ['team_name' => 'unique_slug'];
        $data = ['team_name' => 'the-bears'];

        $v = $this->app['validator']->make($data, $rules);
        $this->assertTrue(! $v->passes());
    }

}
