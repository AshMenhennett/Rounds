<template>
    <div>
        <scale-loader :loading="processing.request || loading.component" :color="loader_color"></scale-loader>

        <bootstrap-alert
            :expression="
                ! loading.component
                && ! loading.players
                && ! players.length
                && teams.length > 0
                && ! error.loading.teams
                && ! error.loading.players"
            alert-type="info"
            message-bold="Uh Oh!"
        >
            <template slot="default">There are no players to choose from. Pick a different team!</template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="
                        ! loading.component &&
                        ! loading.players
                        && error.other"
            alert-type="danger"
            message-bold="Error!"
        >
            <template slot="default">We couldn't prepare your data! Please try again later.</template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="
                        ! loading.component
                        && ! loading.players
                        && ! error.other
                        && (error.loading.teams || error.loading.players)"
            alert-type="danger"
            message-bold="Error!"
        >
            <template slot="default">We couldn't load the necessary data! Please try again later.</template>
        </bootstrap-alert>

        <template v-if="
                    ! loading.component
                    && ! processing.submission
                    && teams.length
                    && ! processing.request
                    && ! error.loading.teams
                    && ! error.loading.players">
            <div class="col-md-4 form-group" :class="error.validation.team ? 'has-error' : ''">
                <select v-model="selected_team.slug" @change="getPlayersForTeam" class="form-control" required>
                    <option v-for="team in teams" :value="team.slug">{{ team.name }}</option>
                </select>
                <div v-if="error.validation.team" class="help-block danger">
                    You need to select a Team.
                </div>
            </div>
        </template>

        <template v-if="
                    ! loading.component
                    && ! processing.submission
                    && players.length
                    && ! processing.request
                    && ! error.loading.players">
            <div class="col-md-4 form-group" :class="error.validation.player ? 'has-error' : ''">
                <select v-model="selected_player.id" class="form-control" required>
                    <option v-for="player in players" :value="player.id">{{ player.name }}</option>
                </select>
                <div v-if="error.validation.player" class="help-block danger">
                    You need to select a Player.
                </div>
            </div>
        </template>

        <div v-if="
                    ! processing.request
                    && ! loading.component
                    && players.length" class="col-md-4 form-group">
            <button @click.prevent="exportData" class="btn btn-primary pull-right">Export Player Quarter Data</button>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                teams: [],
                players: [],
                selected_team: {
                    slug: null
                },
                selected_player: {
                    id: -1
                },
                loading: {
                    component: true,
                    teams: false,
                    players: false
                },
                processing: {
                    request: false
                },
                error: {
                    loading: {
                        teams: false,
                        players: false
                    },
                    validation: {
                        team: false,
                        player: false
                    },
                    other: false
                },
                loader_color: '#0d0394'
            }
        },
        methods: {
            getTeams() {
                this.loading.teams = true;

                this.error.loading.teams = false;
                return this.$http.get('/admin/teams/fetch').then((response) => {
                    this.teams = response.body;

                    if (this.teams.length) {
                        // set default team
                        this.selected_team.slug = this.teams[0].slug;

                        // get players for default team
                        this.getPlayersForTeam();
                    } else {
                        this.loading.component = false;
                    }

                    this.loading.teams = false;
                }, (response) => {
                    this.loading.teams = false;
                    this.loading.component = false;
                    this.error.loading.teams = true;
                });
            },
            getPlayersForTeam() {
                this.loading.players = true;

                this.error.loading.players = false;
                return this.$http.get('/admin/teams/' + this.selected_team.slug + '/players/fetch').then((response) => {
                    this.players = response.body;

                    // set default player
                    if (this.players.length) {
                        this.selected_player.id = this.players[0].id;
                    }

                    this.loading.players = false;
                    this.loading.component = false;
                }, (response) => {
                    this.loading.players = false;
                    this.loading.component = false;
                    this.error.loading.players = true;
                });
            },
            exportData () {
                this.processing.request = true;

                // clear errors
                this.error.validation.team = false;
                this.error.validation.player = false;
                this.error.other = false;

                return this.$http.post('/admin/export/quarters/player', {
                    team: this.selected_team.slug,
                    player: this.selected_player.id
                }).then((response) => {
                    this.processing.request = false;
                    // get probable team name from team slug
                    var teamNameSegments = this.selected_team.slug.replace('-', ' ').split(' ');
                    var teamName = '';
                    teamNameSegments.forEach(function (value, index, arr) {
                        teamName += value.charAt(0).toUpperCase() + value.slice(1).toLowerCase() + (! index >= (arr.length-1) ? ' ' : '');
                    });
                    // save the returned blob to a file
                    FileSaver.saveAs(response.body, 'Player ' + this.selected_player.id + ' - ' + teamName + ' Quarter Data -' + this.getDate() + '.xls');
                }, (response) => {
                    if (response.data.team) {
                        this.error.validation.team = true;
                    } else if (response.data.player || response.status == 400) {
                        this.error.validation.player = true;
                    } else {
                        this.error.other = true;
                    }
                    this.processing.request = false;
                });
            },
            getDate() {
                return moment().format('Y-M-D hh-mm-ss');
            }
        },
        mounted() {
            this.getTeams();
        }
    }
</script>
