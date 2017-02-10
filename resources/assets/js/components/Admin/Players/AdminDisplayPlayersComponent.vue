<template>
    <div class="component">
        <scale-loader :loading="loading" :color="loader_color"></scale-loader>

        <template v-if="! loading">
            <pages v-if="meta && players.length" :pagination="meta.pagination" isFor="adminPlayers" @adminPlayersChangedPage="getPlayers"></pages>

            <ul class="list-group">
                <player v-for="(player, index) in players" :player="player" :index="index" isFor="admin" @adminDestroyedPlayer="destroyPlayer"></player>
            </ul>

            <pages v-if="meta && players.length" :pagination="meta.pagination" isFor="adminPlayers" @adminPlayersChangedPage="getPlayers"></pages>

            <bootstrap-alert
                :expression="no_players && ! loading"
                alert-type="info"
                message-bold="Uh oh!"
            >
                <template slot="default">
                    There are currently no players!
                    <br>
                    Add some with the fields above.
                </template>
            </bootstrap-alert>
        </template>
    </div>
</template>

<script>
    import eventHub from '../../../events.js';
    export default {
        data () {
            return {
                players: [],
                meta: null,
                loading: false,
                no_players: false,
                loader_color: '#0d0394'
            }
        },
        methods: {
            getPlayers(page) {
                this.loading = true;
                return this.$http.get('/admin/players/fetch?page=' + page).then((response) => {
                    this.players = response.body.data;
                    this.meta = response.body.meta;

                    this.loading = false;

                    if (! this.players.length) {
                        this.no_players = true;
                    } else {
                        this.no_players = false;
                    }
                });
            },
            destroyPlayer(index) {
                var id = this.players[index].id;

                this.players.splice(index, 1);

                return this.$http.delete('/admin/players/' + id);
            }
        },
        mounted() {
            this.getPlayers(1);

            // if there are curently no players left in this 'page', load page 1.
            this.$watch(function () {
                    return this.players.length < 1;
                }, function  (newVal, oldVal) {
                    this.getPlayers(1);
            });

            eventHub.$on('AdminAddedPlayers', this.getPlayers);
        }
    }
</script>
