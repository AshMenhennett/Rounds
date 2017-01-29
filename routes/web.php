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

    // view available teams
    Route::get('/teams', 'Coach\TeamsController@index')->name('teams.index');
    // coach joins a team
    Route::post('/teams/{team}', 'Coach\TeamController@store')->name('team.store');
    // removes a coach from a team
    Route::delete('/teams/{team}/coach', 'Coach\TeamController@detach')->name('team.user.detach');
    // display add players and leave team links and stats
    Route::get('/teams/{team}/manage', 'Coach\TeamManagementController@show')->name('team.manage');

    // players for coach
    Route::get('/teams/{team}/players', 'Coach\PlayersController@index')->name('players.index');
    Route::get('/teams/{team}/players/fetch', 'Coach\PlayersController@fetch')->name('players.fetch');

    Route::post('/teams/{team}/players/new', 'Coach\PlayerController@store')->name('player.store');
    Route::get('/teams/{team}/players/{player}/edit', 'Coach\PlayerController@edit')->name('player.edit');
    Route::put('/teams/{team}/players/{player}', 'Coach\PlayerController@update')->name('player.update');
    Route::delete('/teams/{team}/players/{player}', 'Coach\PlayerController@destroy')->name('player.destroy');

    // view all available rounds
    Route::get('/teams/{team}/rounds', 'Coach\RoundsController@index')->name('rounds.index');
    // joins a team to a round + display round form to fill in etc
    Route::get('/teams/{team}/rounds/{round}', 'Coach\RoundController@show')->name('round.show');
    Route::post('/teams/{team}/rounds/{round}/date', 'Coach\RoundDateController@store')->name('round.store.date');
    Route::post('/teams/{team}/rounds/{round}', 'Coach\RoundController@store')->name('round.store');
    // gets player data in relation to a Round
    Route::get('/teams/{team}/rounds/{round}/fetch ', 'Coach\RoundController@fetchPlayers')->name('round.fetch');

    Route::group(['prefix' => 'admin', 'middleware' => ['admin']], function () {

        Route::get('/', 'Admin\AdminDashboardController@index')->name('admin.home');

        Route::get('/players/fetch', 'Admin\AdminPlayersController@fetch')->name('admin.players.fetch');

        Route::post('/players/new', 'Admin\AdminPlayerController@store')->name('admin.player.store');
        Route::get('/players/{player}/edit', 'Admin\AdminPlayerController@edit')->name('admin.player.edit');
        Route::put('/players/{player}', 'Admin\AdminPlayerController@update')->name('admin.player.update');
        Route::delete('/players/{player}', 'Admin\AdminPlayerController@destroy')->name('admin.player.destroy');

        Route::get('/teams/fetch', 'Admin\AdminTeamsController@fetch')->name('admin.teams.fetch');

        Route::post('players/import', 'Admin\AdminImportPlayersController@store')->name('admin.players.import');
        Route::post('players/import/fixed', 'Admin\AdminImportFixedPlayersController@store')->name('admin.players.import.fixed');
    });

});
