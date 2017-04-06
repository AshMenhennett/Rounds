<template>
    <div class="component no-top-mar">
        <scale-loader :loading="loading" :color="loader_color"></scale-loader>

        <div v-if="players.length && ! loading">
            <h4>Players</h4>
            <ul class="list-group">
                <coach-team-player v-for="(player, index) in players" :player="player" type="Normal" :index="index" :team="team" isFor="coach" @coachDestroyedNormalPlayer="deleteNormalPlayer"></coach-team-player>
            </ul>
        </div>

        <div v-if="temp_players.length && ! loading">
            <h4>Temp Players</h4>
            <ul class="list-group temp-players">
                <coach-team-player v-for="(player, index) in temp_players" :player="player" type="Temp" :index="index" :team="team" isFor="coach" @coachDestroyedTempPlayer="deleteTempPlayer"></coach-team-player>
            </ul>
        </div>

        <br />

        <bootstrap-alert
            :expression="(! players.length && ! temp_players.length) && ! loading"
            alert-type="info"
            message-bold="Let's get started!"
        >
            <template slot="default">
                Add some players to your team.
            </template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="success"
            alert-type="success"
            message-bold="Great!"
        >
            <template slot="default">
                Player added successfully!
            </template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="adding_error"
            alert-type="danger"
            message-bold="Error!"
        >
            <template slot="default">
                Please check your input for errors.
            </template>
        </bootstrap-alert>

        <scale-loader :loading="adding" :color="loader_color"></scale-loader>

        <div v-if="! adding">
            <h4>Add a Player</h4>
            <div class="form-group" :class="adding_error ? 'has-error' : ''">
                <label for="name" class="label-control">Player Name</label>
                <input @keyup.enter="addPlayer()" type="text" v-model="name" name="name" id="name" class="form-control" required>
                <div v-if="adding_error" class="help-block danger">
                    You need to enter a name.
                </div>

                <div class="form-group checkbox pull-right">
                    <label for="temp">
                        <input type="checkbox" v-model="temp" name="temp" id="temp" style="margin-top:4px; padding:8px;"> Temporary Player?
                    </label>
                </div>
                <br />
                <br />
                <div class="form-group pull-right">
                    <button @click.prevent="addPlayer()" :disabled="adding" class="btn btn-default pull-right">Add Player</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                players: [],
                temp_players: [],
                // loading- entire component
                loading: true,
                // adding- adding a new player
                adding: false,
                // success- adding a new player
                success: false,
                // name of new player entered
                name: '',
                // new player is temporary?
                temp: false,
                // error adding new Player
                adding_error: false,
                loader_color: '#0d0394'
            }
        },
        props: {
            team: String
        },
        methods: {
            fetchPlayers() {
                // data structure: {"data":[{"player":{"id":0, ..., "rounds":1}, ..., ...]}
                return this.$http.get('/teams/' + this.team + '/players/fetch').then((response) => {
                    for(var i = 0; i < response.body.data.length; i++) {
                        if (response.body.data[i].temp === 0) {
                            this.players.push(response.body.data[i]);
                            continue;
                        }
                        this.temp_players.push(response.body.data[i]);
                    }
                    this.loading = false;
                });
            },
            addPlayer() {
                this.adding = true;
                // reset error
                this.adding_error = false;

                this.success = false;
                return this.$http.post('/teams/' + this.team + '/players/new', {
                    name: this.name,
                    temp: this.temp
                }).then((response) => {
                    if (this.temp === true) {
                        this.temp_players.push({
                            id: response.body.id,
                            name: response.body.name,
                            temp: response.body.temp,
                            rounds: 0
                        });
                    } else {
                        this.players.push({
                            id: response.body.id,
                            name: response.body.name,
                            temp: response.body.temp,
                            rounds: 0
                        });
                    }
                    this.adding = false;
                    this.success = true;
                    this.name = '';
                }, (response) => {
                    this.adding = false;
                    this.adding_error = true;
                    this.success = false;
                });
            },
            deleteNormalPlayer(index) {
                var id  = this.players[index].id;
                this.players.splice(index, 1);
                return this.$http.delete('/teams/' + this.team + '/players/' + id);

                // for (var i = 0; i < this.players.length; i++) {
                //     if (this.players[i].id === id) {
                //         this.players.splice(i, 1);
                //         return this.$http.delete('/teams/' + this.team + '/players/' + id);
                //     }
                // }
                // for (var i = 0; i < this.temp_players.length; i++) {
                //     if (this.temp_players[i].id === id) {
                //         this.temp_players.splice(i, 1);
                //         return this.$http.delete('/teams/' + this.team + '/players/' + id);
                //     }
                // }
            },
            deleteTempPlayer(index) {
                // if (! confirm("Are you sure you want to delete this player?")) {
                //     return;
                // }

                var id  = this.temp_players[index].id;
                this.temp_players.splice(index, 1);
                return this.$http.delete('/teams/' + this.team + '/players/' + id);

                // for (var i = 0; i < this.players.length; i++) {
                //     if (this.players[i].id === id) {
                //         this.players.splice(i, 1);
                //         return this.$http.delete('/teams/' + this.team + '/players/' + id);
                //     }
                // }
                // for (var i = 0; i < this.temp_players.length; i++) {
                //     if (this.temp_players[i].id === id) {
                //         this.temp_players.splice(i, 1);
                //         return this.$http.delete('/teams/' + this.team + '/players/' + id);
                //     }
                // }
            }
        },
        mounted() {
            this.fetchPlayers();
        }
    }
</script>
