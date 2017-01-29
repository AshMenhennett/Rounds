<template>
    <div class="component">
        <scale-loader :loading="loading" :color="loader_color"></scale-loader>

        <pages v-if="meta && players.length" :pagination="meta.pagination" for="adminPlayers" @adminPlayersChangedPage="getPlayers"></pages>

        <ul class="list-group">
            <player v-for="(player, index) in players" :player="player" :index="index" for="admin" @adminDestroyedPlayer="destroyPlayer"></player>
        </ul>

        <pages v-if="meta && players.length" :pagination="meta.pagination" for="adminPlayers" @adminPlayersChangedPage="getPlayers"></pages>

        <bootstrap-alert
            :expression="! players.length && ! loading"
            alert-type="info"
            message-bold="Uh oh!"
        >
            There are currently no players.
            <br>
            Add some with the forms above!
        </bootstrap-alert>
    </div>
</template>

<script>
    import eventHub from '../../../events.js';
    export default {
        data () {
            return {
                players: [],
                meta: null,
                loading: true,
                no_players: false,
                current_page: 1,
                loader_color: '#0d0394'
            }
        },
        methods: {
            getPlayers(page = 1) {
                this.current_page = page;
                return this.$http.get('/admin/players/fetch?page=' + page).then((response) => {
                    this.players = response.body.data;
                    this.meta = response.body.meta;

                    this.loading = false;
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

            this.$watch(
                function () {
                    return this.players.length < 1;
                },
                function  (newVal, oldVal) {
                    if (this.current_page > 1) {
                        this.getPlayers(this.current_page - 1);
                    } else {
                        this.no_players = true;
                    }
                }
            );

            eventHub.$on('AdminAddedPlayers', this.getPlayers);

            this.$on('NoPlayersInCurrentPage', this.getPlayers);
        }
    }
</script>
