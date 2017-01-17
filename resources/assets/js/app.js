
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

Vue.component('players', require('./components/TeamPlayersComponent.vue'));
Vue.component('round-input', require('./components/RoundInputComponent.vue'));

// Vue.component('moon-loader', require('vue-spinner/src/MoonLoader.vue'));
// Vue.component('bounce-loader', require('vue-spinner/src/BounceLoader.vue'));
Vue.component('scale-loader', require('vue-spinner/src/ScaleLoader.vue'));

const app = new Vue({
    el: '#app'
});
