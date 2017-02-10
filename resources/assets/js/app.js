
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


Vue.component('round-input', require('./components/Coach/RoundInputComponent.vue'));
Vue.component('team-players', require('./components/Coach/TeamPlayersComponent.vue'));



// admin dashboard
Vue.component('admin-dashboard', require('./components/Admin/AdminDashboardComponent.vue'));

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

// houses all admin team components
Vue.component('admin-teams-master', require('./components/Admin/Teams/AdminTeamsMasterComponent.vue'));
    // imports teams
    Vue.component('admin-import-teams', require('./components/Admin/Teams/AdminImportTeamsComponent.vue'));
    // creates a team
    Vue.component('admin-create-team', require('./components/Admin/Teams/AdminCreateTeamComponent.vue'));
    // displays all players with pagination
    Vue.component('admin-display-teams', require('./components/Admin/Teams/AdminDisplayTeamsComponent.vue'));

// generic
Vue.component('bootstrap-alert', require('./components/Generic/BootstrapAlertComponent.vue'));
Vue.component('player', require('./components/Generic/PlayerComponent.vue'));
Vue.component('team', require('./components/Generic/TeamComponent.vue'));
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
