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

Route::group(['middleware' => ['auth']], function () {

    Route::get('/home', 'HomeController@index')->name('home');

    // view available teams
    Route::get('/teams', 'TeamsController@index')->name('teams.index');
    // coach joins a team
    Route::post('/teams/{team}', 'TeamController@store')->name('team.store');
    // removes a coach from a team
    Route::delete('/teams/{team}/coach', 'TeamController@detach')->name('team.user.detach');
    // display add players and leave team links and stats
    Route::get('/teams/{team}/manage', 'TeamManagementController@show')->name('team.manage');

    // add players etc
    Route::get('/teams/{team}/players', 'PlayerController@index')->name('players.index');
    Route::get('/teams/{team}/players/fetch', 'PlayerController@fetch')->name('players.fetch');

    Route::post('/teams/{team}/players/new', 'PlayerController@store')->name('player.store');
    Route::get('/teams/{team}/players/{player}/edit', 'PlayerController@edit')->name('player.edit');
    Route::post('/teams/{team}/players/{player}', 'PlayerController@update')->name('player.update');
    Route::delete('/teams/{team}/players/{player}', 'PlayerController@delete')->name('player.delete');

    // view all available rounds
    Route::get('/teams/{team}/rounds', 'RoundsController@index')->name('rounds.index');
    // joins a team to a round + display round form to fill in etc
    Route::get('/teams/{team}/rounds/{round}', 'RoundController@show')->name('round.show');
    Route::post('/teams/{team}/rounds/{round}/date', 'RoundDateController@store')->name('round.store.date');
    Route::post('/teams/{team}/rounds/{round}', 'RoundController@store')->name('round.store');

    Route::get('/teams/{team}/rounds/{round}/fetch ', 'RoundController@fetchPlayers')->name('round.fetch');

});
