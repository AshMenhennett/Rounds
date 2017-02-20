<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/legal/terms', function () {
    return view('legal.terms');
})->name('legal.terms');

Route::get('/legal/privacy', function () {
    return view('legal.privacy');
})->name('legal.privacy');

// TODO: add valid excel format to faq
Route::get('/faq', function () {
    return view('faq');
})->name('faq');

Route::group(['middleware' => ['auth']], function () {

    Route::get('/home', 'Coach\HomeController@index')->name('home');

<<<<<<< HEAD
    Route::get('/teams', 'Coach\Teams\TeamsController@index')->name('coach.teams.index');
    Route::post('/teams/{team}', 'Coach\Teams\TeamController@store')->name('coach.team.store');
    Route::delete('/teams/{team}/coach', 'Coach\Teams\TeamController@detach')->name('coach.team.user.detach');
    Route::get('/teams/{team}/manage', 'Coach\Teams\TeamManagementController@show')->name('coach.team.manage');

    // displays Vue component to view all players within a Team
    Route::get('/teams/{team}/players', 'Coach\Players\PlayersController@index')->name('coach.players.index');
    // gets players for pagination with fractal
    Route::get('/teams/{team}/players/fetch', 'Coach\Players\PlayersController@fetch')->name('coach.players.fetch');

    Route::post('/teams/{team}/players/new', 'Coach\Players\PlayerController@store')->name('coach.player.store');
    Route::get('/teams/{team}/players/{player}/edit', 'Coach\Players\PlayerController@edit')->name('coach.player.edit');
    Route::put('/teams/{team}/players/{player}', 'Coach\Players\PlayerController@update')->name('coach.player.update');
    Route::delete('/teams/{team}/players/{player}', 'Coach\Players\PlayerController@destroy')->name('coach.player.destroy');

    // displays all available rounds that a Team can fill in
    Route::get('/teams/{team}/rounds', 'Coach\Rounds\RoundsController@index')->name('coach.rounds.index');
    // initially displays options for adding a Round to a Team and subsequently displays date input and round input component,
    // as well as destroy button to remove the Team/ Round association
    Route::get('/teams/{team}/rounds/{round}', 'Coach\Rounds\RoundController@show')->name('coach.round.show');
    // adds association for a Round to a Team
    Route::post('/teams/{team}/rounds/{round}', 'Coach\Rounds\RoundController@store')->name('coach.round.store');
    //  stores and updates round date
    Route::post('/teams/{team}/rounds/{round}/date', 'Coach\Rounds\RoundDateController@store')->name('coach.round.store.date');
    //  updates round data for a Team, specifically adding associations in player_round, adding players into this Round, for this Team
    Route::put('/teams/{team}/rounds/{round}', 'Coach\Rounds\RoundController@update')->name('coach.round.update');
    // removes association between a Team and Round
    Route::delete('/teams/{team}/rounds/{round}', 'Coach\Rounds\RoundController@destroy')->name('coach.round.destroy');
    // fetch all team data for a Round
    Route::get('/teams/{team}/rounds/{round}/fetch ', 'Coach\Rounds\RoundController@fetchPlayers')->name('coach.round.fetch');
=======
    Route::get('/teams', 'Coach\TeamsController@index')->name('teams.index');
    Route::post('/teams/{team}', 'Coach\TeamController@store')->name('team.store');
    Route::delete('/teams/{team}/coach', 'Coach\TeamController@detach')->name('team.user.detach');
    Route::get('/teams/{team}/manage', 'Coach\TeamManagementController@show')->name('team.manage');

    Route::get('/teams/{team}/players', 'Coach\PlayersController@index')->name('players.index');
    Route::get('/teams/{team}/players/fetch', 'Coach\PlayersController@fetch')->name('players.fetch');

    Route::post('/teams/{team}/players/new', 'Coach\PlayerController@store')->name('player.store');
    Route::get('/teams/{team}/players/{player}/edit', 'Coach\PlayerController@edit')->name('player.edit');
    Route::put('/teams/{team}/players/{player}', 'Coach\PlayerController@update')->name('player.update');
    Route::delete('/teams/{team}/players/{player}', 'Coach\PlayerController@destroy')->name('player.destroy');

    Route::get('/teams/{team}/rounds', 'Coach\RoundsController@index')->name('rounds.index');
    Route::get('/teams/{team}/rounds/{round}', 'Coach\RoundController@show')->name('round.show');
    Route::post('/teams/{team}/rounds/{round}/date', 'Coach\RoundDateController@store')->name('round.store.date');
    Route::post('/teams/{team}/rounds/{round}', 'Coach\RoundController@store')->name('round.store');
    Route::get('/teams/{team}/rounds/{round}/fetch ', 'Coach\RoundController@fetchPlayers')->name('round.fetch');
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3

    Route::group(['prefix' => 'admin', 'middleware' => ['admin']], function () {

        Route::get('/', 'Admin\AdminDashboardController@index')->name('admin.home');

        // gets players for pagination with fractal
        Route::get('/players/fetch', 'Admin\Players\AdminPlayersController@fetch')->name('admin.players.fetch');

        Route::post('/players/new', 'Admin\Players\AdminPlayerController@store')->name('admin.player.store');
        Route::get('/players/{player}/edit', 'Admin\Players\AdminPlayerController@edit')->name('admin.player.edit');
        Route::put('/players/{player}', 'Admin\Players\AdminPlayerController@update')->name('admin.player.update');
        Route::delete('/players/{player}', 'Admin\Players\AdminPlayerController@destroy')->name('admin.player.destroy');

        Route::post('players/import', 'Admin\Players\AdminImportPlayersController@store')->name('admin.players.import');
        Route::post('players/import/fixed', 'Admin\Players\AdminImportFixedPlayersController@store')->name('admin.players.import.fixed');

        // gets teams as an array, no pagination, no fractal
        Route::get('/teams/fetch', 'Admin\Teams\AdminTeamsController@fetch')->name('admin.teams.fetch');

        // gets teams for pagination with fractal
        Route::get('/teams/fetch/pagination', 'Admin\Teams\AdminTeamsController@fetchForPagination')->name('admin.teams.fetch.pagination');

        Route::post('/teams/new', 'Admin\Teams\AdminTeamController@store')->name('admin.team.store');
        Route::get('/teams/{team}/edit', 'Admin\Teams\AdminTeamController@edit')->name('admin.team.edit');
        Route::put('/teams/{team}', 'Admin\Teams\AdminTeamController@update')->name('admin.team.update');
        Route::delete('/teams/{team}', 'Admin\Teams\AdminTeamController@destroy')->name('admin.team.destroy');

        Route::post('/teams/import', 'Admin\Teams\AdminImportTeamsController@store')->name('admin.teams.import');
<<<<<<< HEAD

        // gets coaches for pagination with fractal
        Route::get('/coaches/fetch', 'Admin\Coaches\AdminCoachesController@fetch')->name('admin.coaches.fetch');

        Route::delete('/coaches/{coach}', 'Admin\Coaches\AdminCoachController@destroy')->name('admin.coach.destroy');

        // gets rounds for pagination with fractal
        Route::get('/rounds/fetch', 'Admin\Rounds\AdminRoundsController@fetch')->name('admin.rounds.fetch');

        Route::post('/rounds/new', 'Admin\Rounds\AdminRoundController@store')->name('admin.round.store');
        Route::get('/rounds/{round}/edit', 'Admin\Rounds\AdminRoundController@edit')->name('admin.round.edit');
        Route::put('/rounds/{round}', 'Admin\Rounds\AdminRoundController@update')->name('admin.round.update');
        Route::delete('/rounds/{round}', 'Admin\Rounds\AdminRoundController@destroy')->name('admin.round.destroy');

        Route::post('rounds/import', 'Admin\Rounds\AdminImportRoundsController@store')->name('admin.rounds.import');
        Route::post('rounds/import/fixed', 'Admin\Rounds\AdminImportFixedRoundsController@store')->name('admin.rounds.import.fixed');

        Route::get('stats/fetch', 'Admin\Stats\AdminStatsController@fetch')->name('admin.stats.fetch');
=======
        //Route::post('/teams/import/fixed', 'Admin\AdminImportFixedTeamsController@store')->name('admin.teams.import.fixed');

        // TODO spice up player import component, like team import component, checks for file empty and no valid data
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3

    });

});
