<template>
    <div class="component">
        <scale-loader :loading="loading.submission || loading.component" :color="loader_color"></scale-loader>

        <bootstrap-alert
            :expression="! loading.submission && success.adding"
            alert-type="success"
            message-bold="Success!"
        >
            Player added.
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! loading.submission && error.adding.name"
            alert-type="danger"
            message-bold="Error!"
        >
            Player needs a name!
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! loading.submission && error.adding.team"
            alert-type="danger"
            message-bold="Error!"
        >
            Player needs a valid Team!
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! loading.submission && error.adding.other"
            alert-type="danger"
            message-bold="Error!"
        >
            Player unable to be added!
        </bootstrap-alert>

        <template v-if="! loading.submission && ! loading.component">
            <h4>Add a Player</h4>
            <div class="form-group" :class="error.adding.name ? 'has-error' : ''">
                <input @keyup.enter="addPlayer()" type="text" v-model="player.name" id="name" placeholder="John Doe" class="form-control">
                <div v-if="error.adding.name" class="help-block danger">
                    You need to enter a name.
                </div>
            </div>

            <div class="form-group">
                <select v-model="player.team" class="form-control">
                    <option v-for="team in teams" :value="team.id">{{ team.name }}</option>
                </select>
            </div>

            <div class="form-group checkbox pull-right">
                <label for="temp">
                    <input type="checkbox" id="temp" v-model="player.temp" style="margin-top:4px; padding:8px;"> Temporary Player?
                </label>
            </div>

            <br>
            <br>

            <div class="form-group pull-right">
                <button @click.prevent="addPlayer()" :disabled="loading.submission" class="btn btn-default pull-right">Add Player</button>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                player: {
                    name: '',
                    team: '',
                    temp: false
                },
                teams: [],
                success: {
                    adding: false
                },
                error: {
                    adding: {
                        name: false,
                        team: false,
                        other: false
                    }
                },
                loading: {
                    submission: false,
                    component: false
                },
                loader_color: '#0d0394'
            }
        },
        methods: {
            addPlayer() {
                this.loading.submission = true;
                return this.$http.post('/admin/players/new', {
                    name: this.player.name,
                    team: this.player.team,
                    temp: this.player.temp
                }).then((response) => {
                    this.success.adding = true;

                    this.error.adding.name = false;
                    this.error.adding.team = false;
                    this.error.adding.other = false;

                    this.loading.submission = false;
                }, (response) => {
                    this.success.adding = false;

                    if (response.data.name) {
                        this.error.adding.name = true;
                    } else if (response.data.team) {
                        this.error.adding.team = true;
                    } else {
                        this.error.adding.other = true;
                    }

                    this.loading.submission = false;
                });
            },
            getTeams() {
                this.loading.component = true;
                return this.$http.get('/admin/teams/fetch').then((response) => {
                    this.teams = response.body;

                    this.setDefaultTeam();
                    this.loading.component = false;
                });
            },
            setDefaultTeam() {
                this.player.team = this.teams[0].id;
            }
        },
        mounted() {
            this.getTeams();

        }
    }
</script>
