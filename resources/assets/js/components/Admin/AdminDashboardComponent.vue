<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <div class="panel panel-default">
                    <div class="panel-heading">Admin Dashboard</div>
                    <div class="panel-body">
                        <ul class="nav nav-pills nav-justified">
                            <li role="presentation" :class="{ 'active' : currentView == 'admin-stats' }"><a href="#" @click.prevent="changeView('admin-stats')">Stats</a></li>
                            <li role="presentation" :class="{ 'active' : currentView == 'admin-teams-master' }"><a href="#" @click.prevent="changeView('admin-teams-master')">Teams</a></li>
                            <li role="presentation" :class="{ 'active' : currentView == 'admin-coaches-master' }"><a href="#" @click.prevent="changeView('admin-coaches-master')">Coaches</a></li>
                            <li role="presentation" :class="{ 'active' : currentView == 'admin-players-master' }"><a href="#" @click.prevent="changeView('admin-players-master')">Players</a></li>
                            <li role="presentation" :class="{ 'active' : currentView == 'admin-rounds-master' }"><a href="#" @click.prevent="changeView('admin-rounds-master')">Rounds</a></li>
                        </ul>

                        <!-- <keep-alive> -->
                            <component :is="currentView"></component>
                        <!-- </keep-alive> -->
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                currentView: 'admin-stats'
            }
        },
        methods: {
            changeView(component) {
                this.currentView = component;
            },
            // Read a page's GET URL variables and return them as an associative array.
            // obtained from: http://stackoverflow.com/questions/4656843/jquery-get-querystring-from-url
            getUrlVars() {
                var vars = [],
                    hash;
                var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
                for (var i = 0; i < hashes.length; i++) {
                    hash = hashes[i].split('=');
                    vars.push(hash[0]);
                    vars[hash[0]] = hash[1];
                }
                return vars;
            }
        },
        mounted() {
            if (this.getUrlVars()['v'] == 'players') {
                this.currentView = 'admin-players-master';
            } else if (this.getUrlVars()['v'] == 'teams') {
                this.currentView = 'admin-teams-master';
            }
        }
    }
</script>
