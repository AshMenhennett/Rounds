
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// coach components
Vue.component('coach-round-input', require('./components/Coach/Rounds/RoundInputComponent.vue'));
Vue.component('coach-team-players', require('./components/Coach/Players/TeamPlayersComponent.vue'));
Vue.component('coach-team-player', require('./components/Coach/Players/PlayerComponent.vue'));

// admin dashboard
Vue.component('admin-dashboard', require('./components/Admin/AdminDashboardComponent.vue'));
// delete all
Vue.component('admin-delete-all', require('./components/Admin/AdminDeleteAllComponent.vue'));

// stats
Vue.component('admin-stats', require('./components/Admin/Stats/AdminStatsComponent.vue'));

// houses all admin player components
Vue.component('admin-players-master', require('./components/Admin/Players/AdminPlayersMasterComponent.vue'));
    // imports players
    Vue.component('admin-import-players', require('./components/Admin/Players/AdminImportPlayersComponent.vue'));
    // creates a player
    Vue.component('admin-create-player', require('./components/Admin/Players/AdminCreatePlayerComponent.vue'));
    // displays all players with pagination
    Vue.component('admin-display-players', require('./components/Admin/Players/AdminDisplayPlayersComponent.vue'));
    // player component
    Vue.component('player', require('./components/Admin/Players/PlayerComponent.vue'));//

// houses all admin team components
Vue.component('admin-teams-master', require('./components/Admin/Teams/AdminTeamsMasterComponent.vue'));
    // imports teams
    Vue.component('admin-import-teams', require('./components/Admin/Teams/AdminImportTeamsComponent.vue'));
    // creates a team
    Vue.component('admin-create-team', require('./components/Admin/Teams/AdminCreateTeamComponent.vue'));
    // displays all players with pagination
    Vue.component('admin-display-teams', require('./components/Admin/Teams/AdminDisplayTeamsComponent.vue'));
    // team component
    Vue.component('team', require('./components/Admin/Teams/TeamComponent.vue'));

// houses all admin player components
Vue.component('admin-coaches-master', require('./components/Admin/Coaches/AdminCoachesMasterComponent.vue'));
    // displays coaches
    Vue.component('admin-display-coaches', require('./components/Admin/Coaches/AdminDisplayCoachesComponent.vue'));
    // coach component
    Vue.component('coach', require('./components/Admin/Coaches/CoachComponent.vue'));

// houses all admin round components
Vue.component('admin-rounds-master', require('./components/Admin/Rounds/AdminRoundsMasterComponent.vue'));
    // creates a round
    Vue.component('admin-create-round', require('./components/Admin/Rounds/AdminCreateRoundComponent.vue'));
    // imports rounds from an excel file
    Vue.component('admin-import-rounds', require('./components/Admin/Rounds/AdminImportRoundsComponent.vue'));
    // displays rounds
    Vue.component('admin-display-rounds', require('./components/Admin/Rounds/AdminDisplayRoundsComponent.vue'));
    // round component
    Vue.component('round', require('./components/Admin/Rounds/RoundComponent.vue'));

// exports player quarter data by team
Vue.component('admin-export-player-quarter-data', require('./components/Admin/Export/AdminExportPlayerQuarterDataByTeamComponent.vue'));

// admin ecosystem buttons
Vue.component('admin-ecosystem-buttons', require('./components/Admin/Ecosystem/AdminDisplayEcosystemButtonsComponent.vue'));
//ecosystem button component
Vue.component('ecosystem-button', require('./components/Admin/Ecosystem/EcosystemButtonComponent.vue'));

// generic
Vue.component('bootstrap-alert', require('./components/Generic/BootstrapAlertComponent.vue'));
Vue.component('pages', require('./components/Generic/PagesComponent.vue'));

// Vue.component('moon-loader', require('vue-spinner/src/MoonLoader.vue'));
// Vue.component('bounce-loader', require('vue-spinner/src/BounceLoader.vue'));
Vue.component('scale-loader', require('vue-spinner/src/ScaleLoader.vue'));

const app = new Vue({
    el: '#app'
});

// fade out flash success message
$(document).ready(function () {
    if ($('#flash-message').length > 0) {
        $('#flash-message').delay(2500).fadeOut(550)
    }
});
