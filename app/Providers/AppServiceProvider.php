<?php

namespace App\Providers;

use App\Team;
use Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Validators
         */
        Validator::extend('team_exists', function($attributes, $value, $parameters) {
            // checks if a team exists
            return Team::find($value);
        });

        Validator::extend('unique_slug', function($attributes, $value, $parameters) {
            // checks if slug'ified version of the team name is unique, compared to existing team slugs
            return ! Team::where('slug', str_slug($value, '-'))->first();
        });

        /**
         * Model Observers
         */
        Team::creating(function ($team) {
            $team->slug = str_slug($team->name, '-');
            return $team;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (env('APP_ENV') === 'production') {
            $this->app['request']->server->set('HTTPS', true);
        }
    }
}
