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

    // teams
    Route::get('/teams', 'TeamsController@index')->name('teams.index');

    Route::post('/teams/{team}', 'TeamController@store')->name('team.store');
    Route::get('/teams/{team}', 'TeamController@show')->name('team.show');
    Route::delete('/teams/{team}/coach', 'TeamController@detach')->name('team.user.detach');

    // players
    Route::get('/teams/{team}/players', 'PlayerController@index')->name('players.index');
    Route::get('/teams/{team}/players/fetch', 'PlayerController@fetch')->name('players.fetch');

    Route::post('/teams/{team}/players/new', 'PlayerController@store')->name('player.store');
    Route::get('/teams/{team}/players/{player}/edit', 'PlayerController@edit')->name('player.edit');
    Route::post('/teams/{team}/players/{player}', 'PlayerController@update')->name('player.update');
    Route::delete('/teams/{team}/players/{player}', 'PlayerController@delete')->name('player.delete');

    // rounds
    Route::get('/teams/{team}/rounds/{round}', 'RoundController@index')->name('round.index');

});
