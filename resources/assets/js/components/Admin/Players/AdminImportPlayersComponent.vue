<template>
    <div class="component">

        <scale-loader :loading="importing" :color="loader_color"></scale-loader>
        <scale-loader :loading="loading.component" :color="loader_color"></scale-loader>

        <bootstrap-alert
            :expression="! importing && success.import"
            alert-type="success"
            message-bold="Success!"
        >
            <template slot="default">Players imported!</template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! importing && (error.validation || error.empty_file || error.bad_date_present)"
            alert-type="danger"
            message-bold="Error!"
        >
            <template slot="default">
                Players unable to be imported!
                <br>
                Please check the file is a valid Excel file.
                <br>
                <a href="/faq#valid-excel" target="_blank" class="alert-link">Example of valid Excel format <span class="glyphicon glyphicon-new-window"></span></a>.
            </template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! importing && error.other"
            alert-type="danger"
            message-bold="Error!"
        >
            <template slot="default">
                Players unable to be imported!
                <br>
                Please try again later.
            </template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! importing && warning.invalid_data_submitted"
            alert-type="warning"
            message-bold="Warning!"
        >
            <template slot="default">
                Some players were unable to be imported!
                <br>
                Please complete the following entries.
                <template v-if="warning.invalid_fixed_data_submitted">
                    <br>
                    <strong>Please fix your data for submission</strong>.
                </template>
            </template>
        </bootstrap-alert>

        <template v-if="! importing && warning.invalid_data_submitted">
            <div v-for="(player, index) in invalid_players">
                <div class="form-group col-no-pad col-md-6 has-error">
                    <label :for="'name-' + index">Name</label>
                    <input class="form-control" type="text" v-model="player.name" :id="'name-' + index" placeholder="John Doe">
                    <div class="help-block danger">
                        You must enter a name no longer than 255 characters.
                    </div>
                </div>
                <div class="form-group col-md-6 has-error">
                    <label :for="'team-' + index">Team</label>
                    <select class="form-control" v-model="player.team" :id="'team-' + index">
                        <option v-for="team in teams" :value="team.id">{{ team.name }}</option>
                    </select>
                    <div class="help-block danger">
                        You must select a team from the options above.
                    </div>
                </div>
            </div>
            <div class="form-group col-md-12">
                <a href="#" @click.prevent="submitFixedPlayers" class="btn btn-primary pull-right">Submit</a>
            </div>
        </template>

        <template v-if="teams.length">
            <template v-if="! importing && ! warning.invalid_data_submitted">
                <h4>Import Players</h4>
                <div class="form-group" :class="error.validation ? 'has-error' : ''">
                    <input type="file" id="file" @change="importPlayers($event)" class="form-control">
                    <div v-if="error.validation" class="help-block danger">
                        You need to select a valid excel file.
                    </div>
                </div>
            </template>
        </template>
        <template v-if="! teams.length && ! loading.component">
            <div class="alert alert-info text-center">
                <p><strong>Uh oh!</strong> You will need to create some teams before you can import players!</p>
                <p>Press the &quot;Teams&quot; tab above.</p>
            </div>
        </template>
    </div>
</template>

<script>
    import eventHub from '../../../events.js';
    export default {
        data() {
            return {
                loading: {
                    component: true
                },
                importing: false,
                error: {
                    validation: false,
                    empty_file: false,
                    bad_date_present: false,
                    other: false
                },
                warning: {
                    invalid_data_submitted: false,
                    invalid_fixed_data_submitted: false
                },
                success: {
                    import: false
                },
                invalid_players: [],
                teams: [],
                loader_color: '#0d0394'
            }
        },
        methods: {
            importPlayers(e) {
                this.importing = true;
                this.success.import = false;

                // reset errors
                this.error.validation = false;
                this.error.empty_file = false;
                this.error.bad_date_present = false;
                this.error.other = false;
                this.warning.invalid_data_submitted = false;

                var formData = new FormData();
                formData.append('players', e.target.files[0]);

                return this.$http.post('/admin/players/import', formData).then((response) => {
                    if (response.data.invalid_player_data.length || response.status != 200) {
                        this.invalid_players = response.data.invalid_player_data;
                        this.warning.invalid_data_submitted = true;

                        this.setDefaultTeams();
                    } else {
                        this.success.import = true;
                    }

                    this.importing = false;

                    eventHub.$emit('AdminAddedPlayers', 1);
                }, (response) => {
                    if (response.data.players) {
                        // validation error
                        this.error.validation = true;
                    } else if (response.status == 400) {
                        // file is considered empty (eg. wrong format)
                        this.error.empty_file = true;
                    } else if (response.status == 406) {
                        // an invalid date was present in uploaded file
                        this.error.bad_date_present = true;
                    } else {
                        // some other error
                        this.error.other = true;
                    }

                    this.importing = false;
                });
            },
            submitFixedPlayers() {
                this.importing = true;

                // reset errors
                this.warning.invalid_fixed_data_submitted = false;

                return this.$http.post('/admin/players/import/fixed', {
                    players: this.invalid_players
                }).then((response) => {
                    if (response.data.invalid_player_data.length || response.status != 200) {
                        this.invalid_players = response.data.invalid_player_data;
                        this.warning.invalid_fixed_data_submitted = true;

                        this.setDefaultTeams();
                    } else {
                        this.success.import = true;

                        this.warning.invalid_data_submitted = false;
                    }
                    this.importing = false;

                    eventHub.$emit('AdminAddedPlayers', 1);
                }, (response) => {
                    this.importing = false;
                });
            },
            getTeams() {
                return this.$http.get('/admin/teams/fetch').then((response) => {
                    this.teams = response.body;

                    this.loading.component = false;
                });
            },
            setDefaultTeams() {
                if (! this.invalid_players.length || ! this.teams.length) {
                    return;
                }

                var self = this;
                // Iterate through the invalid players and set a default value for the team property
                $.map(this.invalid_players, function(value, index) {
                    return [value];
                }).forEach(function (el) {
                    el['team'] = self.teams[0].id;
                });
            }
        },
        mounted() {
            this.getTeams();
        }
    }
</script>
