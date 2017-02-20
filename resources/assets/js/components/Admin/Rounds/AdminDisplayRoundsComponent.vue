<template>
    <div class="component">
        <scale-loader :loading="loading" :color="loader_color"></scale-loader>

        <template v-if="! loading">
            <pages v-if="meta && rounds.length" :pagination="meta.pagination" isFor="adminRounds" @adminRoundsChangedPage="getRounds"></pages>

            <ul class="list-group">
                <round v-for="(round, index) in rounds" :round="round" :index="index" isFor="admin" @adminDestroyedRound="destroyRound"></round>
            </ul>

            <pages v-if="meta && rounds.length" :pagination="meta.pagination" isFor="adminRounds" @adminRoundsChangedPage="getRounds"></pages>

            <bootstrap-alert
                :expression="no_rounds && ! loading"
                alert-type="info"
                message-bold="Uh oh!"
            >
                <template slot="default">
                    There are currently no rounds!
                    <br>
                    Create some using the fields above.
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
                rounds: [],
                meta: null,
                loading: false,
                no_rounds: false,
                loader_color: '#0d0394'
            }
        },
        methods: {
            getRounds(page) {
                this.loading = true;
                return this.$http.get('/admin/rounds/fetch?page=' + page).then((response) => {
                    this.rounds = response.body.data;
                    this.meta = response.body.meta;

                    this.loading = false;

                    if (! this.rounds.length) {
                        this.no_rounds = true;
                    } else {
                        this.no_rounds = false;
                    }
                });
            },
            destroyRound(index) {
                var id = this.rounds[index].id;

                this.rounds.splice(index, 1);

                return this.$http.delete('/admin/rounds/' + id);
            }
        },
        mounted() {
            this.getRounds(1);

            // if there are curently no rounds left in this 'page', load page 1.
            this.$watch(function () {
                    return this.rounds.length < 1;
                }, function  (newVal, oldVal) {
                    this.getRounds(1);
            });

            eventHub.$on('AdminAddedRounds', this.getRounds);
        }
    }
</script>
