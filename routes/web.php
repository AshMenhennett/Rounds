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

Route::get('/faq', function () {
    return view('faq');
})->name('faq');

Route::group(['middleware' => ['auth']], function () {

    Route::get('/ecosystem', 'EcosystemController@index')->name('ecosystem');
    Route::get('/view/files/pdf/{fileName}', 'Files\ViewPDFFileController@show')->name('view.pdf');

    Route::get('/home', 'Coach\HomeController@index')->name('home');

    Route::get('/teams', 'Coach\Teams\TeamsController@index')->name('coach.teams.index');
    Route::post('/teams/{team}', 'Coach\Teams\TeamController@store')->name('coach.team.store');
    Route::delete('/teams/{team}/coach', 'Coach\Teams\TeamController@detach')->name('coach.team.user.detach');
    Route::get('/teams/{team}/manage', 'Coach\Teams\TeamManagementController@show')->name('coach.team.manage');

    // gets the status of a Team's ability to set best players for a Round
    Route::get('/teams/{team}/bestPlayersAllowed/status', 'Coach\Teams\TeamBestPlayerAllowedStatusController@index')->name('coach.teams.bestPlayer.index');

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

    Route::group(['prefix' => 'admin', 'middleware' => ['admin']], function () {

        Route::get('/', 'Admin\AdminDashboardController@index')->name('admin.home');

        Route::get('/examples', function () {
            return view('admin.examples');
        })->name('admin.examples');

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
        // gets players for a given team
        Route::get('/teams/{team}/players/fetch', 'Admin\Teams\AdminTeamPlayersController@fetch')->name('admin.team.players.fetch');

        // gets teams for pagination with fractal
        Route::get('/teams/fetch/pagination', 'Admin\Teams\AdminTeamsController@fetchForPagination')->name('admin.teams.fetch.pagination');

        Route::post('/teams/new', 'Admin\Teams\AdminTeamController@store')->name('admin.team.store');
        Route::get('/teams/{team}/edit', 'Admin\Teams\AdminTeamController@edit')->name('admin.team.edit');
        Route::put('/teams/{team}', 'Admin\Teams\AdminTeamController@update')->name('admin.team.update');
        Route::delete('/teams/{team}', 'Admin\Teams\AdminTeamController@destroy')->name('admin.team.destroy');

        Route::post('/teams/import', 'Admin\Teams\AdminImportTeamsController@store')->name('admin.teams.import');

        // Toggles whether a Coach can fill in the Best Players section of the Coach/RoundInputComponent.vue
        Route::put('/teams/{team}/bestPlayersAllowed/toggle', 'Admin\Teams\AdminTeamBestPlayersAllowedToggleController@update')->name('admin.team.bestPlayersAllowed.toggle');

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

        Route::group(['prefix' => 'export'], function () {
            Route::get('/', 'Admin\Export\AdminExportDataController@index')->name('admin.export.index');

            // includes both best players and second best players (team spirit) and their votes
            Route::post('/best-players', 'Admin\Export\AdminExportBestPlayersByTeamController@export')->name('admin.export.bestPlayers');
            Route::post('/all/team', 'Admin\Export\AdminExportAllByTeamController@export')->name('admin.export.allByTeam');
            Route::post('/quarters/player', 'Admin\Export\AdminExportPlayerQuarterDataByTeamController@export')->name('admin.export.playerQuarters');
            Route::post('/quarters/team', 'Admin\Export\AdminExportTeamQuarterDataController@export')->name('admin.export.teamQuarters');
        });

        Route::delete('/delete', 'Admin\AdminDeleteAllController@destroy')->name('admin.delete.all');

        Route::group(['prefix' => 'ecosystem'], function () {
            Route::get('/buttons', 'Admin\Ecosystem\AdminEcosystemManagementController@index')->name('admin.ecosystem.index');
            Route::post('/buttons/new', 'Admin\Ecosystem\AdminEcosystemManagementController@store')->name('admin.ecosystem.button.store');
            Route::delete('/buttons/{button}', 'Admin\Ecosystem\AdminEcosystemManagementController@destroy')->name('admin.ecosystem.button.destroy');
        });

    });

});
