<template>
    <div class="component">
        <scale-loader :loading="loading" :color="loader_color"></scale-loader>

        <bootstrap-alert
                :expression="! loading && error.loading"
                alert-type="danger"
                message-bold="Error!"
            >
                <template slot="default">
                    We are currently unable to show you the stats for this application.
                </template>
            </bootstrap-alert>

        <template v-if="! loading && ! error.loading">
            <div class="text-center">
                <h4>Club Stats</h4>
                <p>Registered Coaches count: <strong>{{ coaches.length }}</strong></p>
                <p>Teams count: <strong>{{ teams.length }}</strong></p>
                <p>Players count: <strong>{{ players.length }}</strong></p>
                <p>Created Rounds count: <strong>{{ rounds.length }}</strong></p>
                <p>Filled in Rounds count: <strong>{{ filledinRoundsCount }}</strong></p>
                <p>Teams with assigned coaches count: <strong>{{ teamsWithCoaches.length }}</strong></p>
                <div class="help-block">
                    Note: You are also a coach.
                </div>
            </div>
        </template>

    </div>
</template>

<script>
    export default {
        data () {
            return {
                teams: [],
                players: [],
                rounds: [],
                coaches: [],
                teamsWithCoaches: [],
                filledinRoundsCount: 0,

                loading: true,
                loader_color: '#0d0394',
                error: {
                    loading: false
                }
            }
        },
        methods: {
            getStats() {
                // reset errors
                this.error.loading = false;

                return this.$http.get('/admin/stats/fetch').then((response) => {
                    this.teams = response.body.teams;
                    this.players = response.body.players;
                    this.rounds = response.body.rounds;
                    this.coaches = response.body.coaches;
                    this.teamsWithCoaches = response.body.teamsWithCoaches;
                    this.filledinRoundsCount = response.body.filledinRoundsCount;

                    this.loading = false;
                }, (response) => {
                    this.loading = false;
                    this.error.loading = true;
                });
            }
        },
        mounted() {
            this.getStats();
        }
    }
</script>

