<template>
    <div class="component">

        <scale-loader :loading="importing" :color="loader_color"></scale-loader>

        <bootstrap-alert
            :expression="! importing && success.import"
            alert-type="success"
            message-bold="Success!"
        >
            Players imported.
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! importing && (error.import || error.other)"
            alert-type="danger"
            message-bold="Error!"
        >
            Players unable to be imported!
            <br>
            Please check the file is a valid Excel file.
            <br>
            <a href="/faq#valid-excel" class="alert-link">Example of a valid Excel format</a>.
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! importing && warning.invalid_data_submitted"
            alert-type="warning"
            message-bold="Warning!"
        >
            Some players were unable to be imported!
            <br />
            Please complete the following entries.
            <template v-if="warning.invalid_fixed_data_submitted">
                <strong>Please fix your data for submission</strong>.
            </template>
        </bootstrap-alert>

        <template v-if="! importing && warning.invalid_data_submitted">
            <div v-for="(player, index) in invalid_players">
                <div class="form-group col-no-pad col-md-6">
                    <label :for="'name-' + index">Name</label>
                    <input type="text" v-model="player.name" :id="'name-' + index" class="form-control">
                </div>
                <div class="form-group col-md-6">
                    <label :for="'team-' + index">Team</label>
                    <select class="form-control" v-model="player.team" :id="'team-' + index">
                        <option v-for="team in teams" :value="team.id">{{ team.name }}</option>
                    </select>
                </div>
            </div>
            <div class="form-group col-md-12">
                <a href="#" @click.prevent="submitFixedPlayers" class="btn btn-primary pull-right">Submit</a>
            </div>
        </template>

        <template v-if="! importing && ! warning.invalid_data_submitted">
            <h4>Import Players</h4>
            <div class="form-group" :class="error.import ? 'has-error' : ''">
                <input type="file" @change="importPlayers($event)" id="file" class="form-control">
                <div v-if="error.import" class="help-block danger">
                    You need to select a valid excel file.
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import eventHub from '../../../events.js';
    export default {
        data() {
            return {
                importing: false,
                error: {
                    import: false,
                    other: false,
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

                eventHub.$emit('AdminAddedPlayers', 1);

                var formData = new FormData();
                formData.append('players', e.target.files[0]);

                return this.$http.post('/admin/players/import', formData).then((response) => {
                    if (response.data.invalid_player_data.length) {
                        this.invalid_players = response.data.invalid_player_data;
                        this.warning.invalid_data_submitted = true;
                        this.getTeams();
                    } else {
                        this.success.import = true;
                    }
                    this.importing = false;
                    this.error.import = false;
                    this.error.other = false;


                }, (response) => {
                    if (response.data.players) {
                        this.error.import = true;
                    } else {
                        this.error.other = true;
                    }
                    this.importing = false;
                    this.warning.invalid_data_submitted = false;
                    this.success.import = false;
                });
            },
            submitFixedPlayers() {
                this.importing = true;

                eventHub.$emit('AdminAddedPlayers');

                return this.$http.post('/admin/players/import/fixed', {
                    players: this.invalid_players
                }).then((response) => {
                    if (response.data.invalid_player_data.length) {
                        this.invalid_players = response.data.invalid_player_data;
                        this.warning.invalid_fixed_data_submitted = true;
                    } else {
                        this.success.import = true;
                        this.warning.invalid_fixed_data_submitted = false;
                        this.warning.invalid_data_submitted = false;
                    }
                    this.importing = false;


                }, (response) => {
                    this.importing = false;
                });
            },
            getTeams() {
                return this.$http.get('/admin/teams/fetch').then((response) => {
                    this.teams = response.body;
                    this.setDefaultTeams();
                });
            },
            setDefaultTeams() {
                var self = this;
                // iterate through players and set new default value for teams.
                $.map(this.invalid_players, function(value, index) {
                    return [value];
                }).forEach(function (el) {
                    el['team'] = self.teams[0].id;
                });
            }
        }
    }
</script>
