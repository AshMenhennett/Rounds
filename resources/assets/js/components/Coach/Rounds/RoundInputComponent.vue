<template>
    <div class="round-input component">
        <scale-loader :loading="loading" :color="loader_color"></scale-loader>

        <div v-if="players.length && ! loading">
            <h4>Select Players</h4>
            <ul class="list-group">
                <li v-for="player in players" :class="player.selected ? 'selected-player' : ''" class="list-group-item text-center" @click.prevent="setSelected(players, player.id)">
                    <h4>{{ player.name }} <small>{{ player.rounds }} round(s).</small></h4>
                </li>
            </ul>
        </div>

        <div v-if="temp_players.length && ! loading">
            <h4>Select Temp Players</h4>
            <ul class="list-group temp-players">
                <li v-for="player in temp_players" :class="player.selected ? 'selected-player' : ''" class="list-group-item text-center" @click.prevent="setSelected(temp_players, player.id)">
                    <h4>{{ player.name }} <small>{{ player.rounds }} rounds.</small></h4>
                </li>
            </ul>
        </div>

        <bootstrap-alert
            :expression="! players.length && ! temp_players.length && ! loading"
            alert-type="info"
            message-bold="Let's get started!"
        >
            <template slot="default">
                Let's add some players to your team!
            </template>
        </bootstrap-alert>

        <scale-loader :loading="adding" :color="loader_color"></scale-loader>

        <div v-if="! adding">
            <h4>Add a Player</h4>
            <div class="form-group" :class="adding_error ? 'has-error' : ''">
                <input @keyup.enter="addPlayer()" type="text" v-model="name" name="name" id="name" class="form-control" placeholder="Player Name">
                <div v-if="errors.adding_error" class="help-block danger">
                    You need to enter a name.
                </div>
                <br />
                <div class="form-group pull-right">
                    <button @click.prevent="addPlayer()" :disabled="adding" class="btn btn-default pull-right">Add Player</button>
                </div>
            </div>
        </div>

        <hr v-if="! loading && (players.length || temp_players.length)" class="round-input" />

        <div v-if="selected_players.length && ! loading">
            <h4>Enter Quarters</h4>
            <div class="form-group" v-for="player in selected_players" :class="player.round.quarters.count > 4 || (errors.quarter_input.invalid && player.id === errors.quarter_input.player_id) ? 'has-error' : ''">
                <label :for="'player' + player.id" class="label-control">{{ player.name }} *</label>
                <input type="number" :id="'player' + player.id" name="name" v-model="player.round.quarters.count" id="name" class="form-control" min="1" max="4" step="1">
                <div class="input-group">
                    <div class="input-group-addon">Reason for amount of quarters played</div>
                    <input type="text" v-model="player.round.quarters.reason" class="form-control">
                </div>
                <div v-if="player.round.quarters.count > 4 || (errors.quarter_input.invalid && player.id === errors.quarter_input.player_id)" class="help-block danger">
                    A quarter must be a numeric value of no more than 4.
                </div>
            </div>

            <hr v-if="! loading" class="round-input" />

            <template v-if="can_set_best_players">
                <h4>Select Best Player</h4>
                <div class="form-group">
                    <select name="best-player" id="best-player" v-model="best_player" class="form-control">
                        <option value="-1">&nbsp;</option>
                        <option v-for="player in selected_players" :value="player.id">{{ player.name }}</option>
                    </select>
                </div>

                <h4>Select 2nd Best Player</h4>
                <div class="form-group">
                    <select name="second-best-player" id="second-best-player" v-model="second_best_player" class="form-control">
                        <option value="-1">&nbsp;</option>
                        <option v-for="player in selected_players" :value="player.id">{{ player.name }}</option>
                    </select>
                </div>
            </template>
        </div>

        <bootstrap-alert
            :expression="! selected_players.length && (players.length > 0 || temp_players.length > 0) && ! loading"
            alert-type="info"
            message-bold="Select some players!"
        >
            <template slot="default">
                Then you can fill in their quarters and some select best players.
            </template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="errors.submission_error && ! errors.quarter_input.invalid"
            alert-type="danger"
            message-bold="Error!"
        >
            <template slot="default">
                Please check your input before submitting again.
            </template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="errors.submission_error && errors.quarter_input.invalid"
            alert-type="danger"
            message-bold="Error!"
        >
            <template slot="default">
                Please check your quarters input before submitting again.
            </template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="success"
            alert-type="success"
            message-bold="Great!"
        >
            <template slot="default">
                Your round was saved!
            </template>
            <template slot="extra">
                <p><a :href="'/teams/' + team + '/rounds'">&laquo; Go back to Rounds</a></p>
            </template>
        </bootstrap-alert>

        <scale-loader :loading="submitting" :color="loader_color"></scale-loader>

        <br />
        <button v-if="! submitting && selected_players.length" class="btn btn-primary btn-block btn-lg btn-round-input" @click.prevent="saveRound">{{ editing ? 'Update' : 'Save' }}</button>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                players: [],
                temp_players: [],
                selected_players: [],
                can_set_best_players: true,
                best_player: Number,
                second_best_player: Number,
                // loading- entire component
                loading: true,
                // adding- adding a new player
                adding: false,
                // submitting- the data to server
                submitting: false,
                // name of new player entered
                name: '',
                // user is editing the data
                editing: false,
                errors: {
                    // error adding new Player
                    adding_error: false,
                    // general error submitting form
                    submission_error: false,
                    quarter_input: {
                        // whether the quarter input is invalid or not
                        invalid: false,
                        // player id associated with invalid quarter input
                        player_id: Number
                    }
                },
                // submission was successful
                success: false,
                loader_color: '#0d0394'
            }
        },
        props: {
            team: String,
            round: String
        },
        methods: {
            fetchPlayers() {
                // data structure: {"data":[{"player":{"id":0, ..., "rounds":1, "round":{"exists": 1, best_player":1, "second_best_player":0, "quarters":2}}, ..., ...]}
                // exists-- player was previously saved under this round
                // if true, this means the user is editing the form and it has been previously filled in.
                return this.$http.get('/teams/' + this.team + '/rounds/' + this.round + '/fetch').then((response) => {
                    for (var i = 0; i < response.body.data.length; i++) {
                        // adding a selected prop, to be used when player is 'selected'
                        Vue.set(response.body.data[i], 'selected', false);

                        if (response.body.data[i].round.exists === 1) {
                            // assume the user has already filled out this form and is now editing it
                            // if a Player already exists under this round,
                            // we only want to set selected to true for existing players.
                            this.editing = true;
                        }

                        // push players to correct array
                        if (response.body.data[i].temp === 0) {
                            this.players.push(response.body.data[i]);
                        } else {
                            this.temp_players.push(response.body.data[i]);
                        }
                    }

                    if (this.editing) {
                        this.setExistsSelected();
                    } else {
                        // there maybe 'recent' players.
                        // if they exist, we will set them to selected
                        this.setRecentSelected();
                    }
                    this.loading = false;
                });
            },
            getBestPlayersAllowedStatus() {
                return this.$http.get('/teams/' + this.team + '/bestPlayersAllowed/status').then((response) => {
                    this.can_set_best_players = response.data;
                });
            },
            addPlayer() {
                this.adding = true;
                return this.$http.post('/teams/' + this.team + '/players/new', {
                    name: this.name,
                    temp: 1
                }).then((response) => {
                    this.temp_players.push({
                        id: response.body.id,
                        name: response.body.name,
                        temp: response.body.temp,
                        recent: 0,
                        selected: false,
                        rounds: 0,
                        round: {
                            best_player: 0,
                            exists: 0,
                            quarters: {
                                count: 0,
                                reason: ''
                            },
                            second_best_player: 0
                        }
                    });
                    // add this player to the selected_players array and set selected to true
                    this.setSelected(this.temp_players, response.body.id)

                    this.adding = false;
                    this.errors.adding_error = false;
                }, (response) => {
                    this.adding = false;
                    this.errors.adding_error = true;
                });
            },
            setSelected(arr, id) {
                // set or unset the selected value for a player in a given array
                for (var i = 0; i < arr.length; i++) {
                    if (arr[i].id === id) {
                        // toggle the selected truth
                        arr[i].selected = (arr[i].selected ? false : true);
                        var j = this.selected_players.indexOf(arr[i]);
                        if (j > -1) {
                            // player is already added to selected_players
                            this.selected_players.splice(j, 1);
                        } else {
                            // player is not in selected_players
                            this.selected_players.push(arr[i]);
                        }
                    }
                }
            },
            setExistsSelected() {
                // sets all players that were already entered in as part of this round
                for (var i = 0; i < this.players.length; i++) {
                    if (this.players[i].round.exists == 1) {
                        this.setSelected(this.players, this.players[i].id);
                    }

                    // set best players here, as only need default value if there are existing players
                    if (this.players[i].round.best_player == 1) {
                        this.best_player = this.players[i].id;
                    }
                    if (this.players[i].round.second_best_player == 1) {
                        this.second_best_player = this.players[i].id;
                    }
                }

                for (var i = 0; i < this.temp_players.length; i++) {
                    if (this.temp_players[i].round.exists == 1) {
                        this.setSelected(this.temp_players, this.temp_players[i].id);
                    }

                    // set best players here, as only need default value if there are existing players
                    if (this.temp_players[i].round.best_player == 1) {
                        this.best_player = this.temp_players[i].id;
                    }
                    if (this.temp_players[i].round.second_best_player == 1) {
                        this.second_best_player = this.temp_players[i].id;
                    }
                }
            },
            setRecentSelected() {
                // sets all players with a recent value of 1 to be pre-selected
                for (var i = 0; i < this.players.length; i++) {
                    if (this.players[i].recent == 1) {
                        // setting 'recent' players to be pre-selected
                        this.setSelected(this.players, this.players[i].id);
                    }
                }
                for (var i = 0; i < this.temp_players.length; i++) {
                    if (this.temp_players[i].recent == 1) {
                        this.setSelected(this.temp_players, this.temp_players[i].id);
                    }
                }
            },
            setBestPlayers() {
                for (var i = 0; i < this.selected_players.length; i++) {
                    // reset best players properties on players
                    if (this.selected_players[i].round.best_player === 1) {
                        this.selected_players[i].round.best_player = 0;
                    }
                    if (this.selected_players[i].round.second_best_player === 1) {
                        this.selected_players[i].round.second_best_player = 0;
                    }

                    if (! this.can_set_best_players) {
                        // if we can't set best players, don't set default options for select elements
                        continue;
                    }

                    // set best and second best player attributes, depending on value of model bound to select elements
                    if (this.selected_players[i].id === this.best_player) {
                        this.selected_players[i].round.best_player = 1;
                    }
                    if (this.selected_players[i].id === this.second_best_player) {
                        this.selected_players[i].round.second_best_player = 1;
                    }
                }
            },
            saveRound() {
                this.setBestPlayers();
                this.submitting = true;

                // reset success
                this.success = false;

                // reset all errors regarding submission
                this.errors.submission_error = false;
                this.errors.quarter_input.invalid = false;
                this.errors.quarter_input.player_id = -1;

                return this.$http.put('/teams/' + this.team + '/rounds/' + this.round, {
                    players: this.selected_players
                }).then((response) => {
                    this.submitting = false;

                    // user can 'edit' data now that there was a successful post to the server
                    this.editing = true;
                    this.success = true;

                    // redirect outta here, to round listings ?
                    // window.location.href = '/teams/' + this.team;
                }, (response) => {
                    this.errors.submission_error = true;
                    if (response.status == 422) {
                        this.errors.quarter_input.invalid = true;
                        this.errors.quarter_input.player_id = response.body.player_id;
                    }
                    this.submitting = false;
                    this.success = false;
                });
            }
        },
        mounted() {
            this.fetchPlayers();
            this.getBestPlayersAllowedStatus();
        }
    }
</script>
