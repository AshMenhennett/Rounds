<template>
    <div>
        <scale-loader :loading="loading" :color="loader_color"></scale-loader>

        <h4>Players</h4>
        <ul v-if="players.length && ! loading" class="list-group">
            <li v-for="player in players" class="list-group-item text-center">
                <h4>{{ player.name }} <small>{{ player.rounds }} round(s).</small></h4>
                <span v-show="player.rounds === 0">
                    <a :href="'/teams/' + team + '/players/' + player.id + '/edit'" class="text-info"><span class="glyphicon glyphicon-pencil"></span></a>
                    &nbsp;
                    <a href="#" @click.prevent="deletePlayer(player.id)" class="text-danger"><span class="glyphicon glyphicon-remove"></span></a>
                </span>
            </li>
        </ul>
        <div v-if="! players.length && ! loading">There are currently no players for this team.</div>

        <br />

        <h4>Temp Players</h4>
        <ul v-if="temp_players.length && ! loading" class="list-group temp-players">
            <li v-for="player in temp_players" class="list-group-item text-center">
                <h4>{{ player.name }} <small>{{ player.rounds }} round(s).</small></h4>
                <span v-show="player.rounds === 0">
                    <a :href="'/teams/' + team + '/players/' + player.id + '/edit'" class="text-info"><span class="glyphicon glyphicon-pencil"></span></a>
                    &nbsp;
                    <a href="#" @click.prevent="deletePlayer(player.id)" class="text-danger"><span class="glyphicon glyphicon-remove"></span></a>
                </span>
            </li>
        </ul>
        <div v-if="! temp_players.length && ! loading">There are currently no temporary players for this team.</div>

        <br />

        <h4>Add a Player</h4>
        <scale-loader :loading="adding" :color="loader_color"></scale-loader>
        <div class="form-group" :class="adding_error ? 'has-error' : ''">
            <label for="name" class="label-control">Player Name</label>
            <input @keyup.enter="addPlayer()" type="text" v-model="name" name="name" id="name" class="form-control" required>
            <div v-if="adding_error" class="help-block danger">
                You need to enter a name.
            </div>
            <br />
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
                    this.adding_error = false;
                }, (response) => {
                    this.adding = false;
                    this.adding_error = true;
                });
            },
            deletePlayer(id) {
                for (var i = 0; i < this.players.length; i++) {
                    if (this.players[i].id === id) {
                        this.players.splice(i, 1);
                        return this.$http.delete('/teams/' + this.team + '/players/' + id);
                    }
                }
                for (var i = 0; i < this.temp_players.length; i++) {
                    if (this.temp_players[i].id === id) {
                        this.temp_players.splice(i, 1);
                        return this.$http.delete('/teams/' + this.team + '/players/' + id);
                    }
                }
            }
        },
        mounted() {
            this.fetchPlayers();
        }
    }
</script>
