<template>
    <div class="component">
        <scale-loader :loading="processing.submission || loading.component" :color="loader_color"></scale-loader>

        <bootstrap-alert
            :expression="! processing.submission && success.submission"
            alert-type="success"
            message-bold="Success!"
        >
            <template slot="default">Player was created!</template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! processing.submission && (error.validation.team || error.validation.name)"
            alert-type="danger"
            message-bold="Error!"
        >
            <template slot="default">
                Some errors occured processing your submission!
                <br>
                Please check your input.
            </template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! processing.submission && error.other"
            alert-type="danger"
            message-bold="Error!"
        >
            <template slot="default">
                We were unable to create the player at this moment!
                <br>
                Try again later.
            </template>
        </bootstrap-alert>

        <template v-if="! processing.submission && ! loading.component">
            <template v-if="teams.length">
                <h4>Create a Player</h4>
                <div class="form-group" :class="error.validation.name ? 'has-error' : ''">
                    <input @keyup.enter="addPlayer" type="text" v-model="player.name" id="name" placeholder="John Doe" class="form-control">
                    <div v-if="error.validation.name" class="help-block danger">
                        You must enter a name no longer than 255 characters.
                    </div>
                </div>
                <div class="form-group" :class="error.validation.team ? 'has-error' : ''">
                    <select v-model="player.team" class="form-control">
                        <option v-for="team in teams" :value="team.id">{{ team.name }}</option>
                    </select>
                    <div v-if="error.validation.team" class="help-block danger">
                        You must select a team from the options above.
                    </div>
                </div>

                <div class="form-group checkbox pull-right">
                    <label for="temp">
                        <input type="checkbox" id="temp" v-model="player.temp" style="margin-top:4px; padding:8px;"> Temporary Player?
                    </label>
                </div>

                <br>
                <br>

                <div class="form-group pull-right">
                    <button @click.prevent="addPlayer" :disabled="processing.submission" class="btn btn-default pull-right">Create Player</button>
                </div>
            </template>
            <template v-if="! teams.length">
                <div class="alert alert-info text-center">
                    <p><strong>Uh oh!</strong> You will need to create some teams before you can add players here!</p>
                    <p>Press the &quot;Teams&quot; tab above.</p>
                </div>
            </template>
        </template>
    </div>
</template>

<script>
    import eventHub from '../../../events.js';
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
                    submission: false
                },
                error: {
                    validation: {
                        name: false,
                        team: false
                    },
                    other: false
                },
                processing: {
                    submission: false,
                },
                loading: {
                    component: false
                },
                loader_color: '#0d0394'
            }
        },
        methods: {
            addPlayer() {
                this.success.submission = false;

                this.processing.submission = true;

                // clear errors, as not to display old errors again
                this.error.validation.name = false;
                this.error.validation.team = false;
                this.error.other = false;

                return this.$http.post('/admin/players/new', {
                    name: this.player.name,
                    team: this.player.team,
                    temp: this.player.temp
                }).then((response) => {
                    // clear input
                    this.player.name = '';

                    this.success.submission = true;

                    this.error.validation.name = false;
                    this.error.validation.team = false;
                    this.error.other = false;

                    this.processing.submission = false;

                    eventHub.$emit('AdminAddedPlayers', 1);
                }, (response) => {
                    // clear input
                    this.player.name = '';

                    if (response.data.name) {
                        this.error.validation.name = true;
                    } else if (response.data.team) {
                        this.error.validation.team = true;
                    } else {
                        this.error.other = true;
                    }

                    this.processing.submission = false;
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
                if (! this.teams.length) {
                    return;
                }
                this.player.team = this.teams[0].id;
            }
        },
        mounted() {
            this.getTeams();
        }
    }
</script>
