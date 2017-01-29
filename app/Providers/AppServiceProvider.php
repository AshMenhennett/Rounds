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
        Validator::extend('team_exists', function($attributes, $value, $parameters) {
            return Team::find($value);
        });

        // expects $value to be an array of objects. Objects will have a team and name property.
        // Validator::extend('players_team_exists', function($attributes, $value, $parameters) {
        //     $players = collect($value);
        //     $players->each(function ($player) {
        //        if (! Team::find($player['team'])) {
        //             return false;
        //        }
        //     });

        //     return true;
        // });
        //Validator::extend('has_valid_player_data', function($attributes, $value, $parameters) {
            // $has_invalid_player_data = false;

            // $players = collect($value);
            // $players->each(function ($player) use ($has_invalid_player_data) {
            //     if ($player['name'] == '' || $player['name'] == null) {
            //         $has_invalid_player_data = true;
            //     }
            // });
            // return $has_invalid_player_data;
        //});
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
