<template>
    <div class="component">
        <scale-loader :loading="loading" :color="loader_color"></scale-loader>

        <template v-if="! loading">
            <pages v-if="meta && teams.length" :pagination="meta.pagination" isFor="adminTeams" @adminTeamsChangedPage="getTeams"></pages>

            <ul class="list-group">
                <team v-for="(team, index) in teams" :team="team" :index="index" isFor="admin" @adminDestroyedTeam="destroyTeam"></team>
            </ul>

            <pages v-if="meta && teams.length" :pagination="meta.pagination" isFor="adminTeams" @adminTeamsChangedPage="getTeams"></pages>

            <bootstrap-alert
                :expression="no_teams && ! loading"
                alert-type="info"
                message-bold="Uh oh!"
            >
                <template slot="default">
                    There are currently no teams!
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
                teams: [],
                meta: null,
                loading: false,
                no_teams: false,
                loader_color: '#0d0394'
            }
        },
        methods: {
            getTeams(page) {
                this.loading = true;
                return this.$http.get('/admin/teams/fetch/pagination?page=' + page).then((response) => {
                    this.teams = response.body.data;
                    this.meta = response.body.meta;

                    this.loading = false;

                    if (! this.teams.length) {
                        this.no_teams = true;
                    } else {
                        this.no_teams = false;
                    }
                });
            },
            destroyTeam(index) {
                var slug = this.teams[index].slug;

                this.teams.splice(index, 1);

                return this.$http.delete('/admin/teams/' + slug);
            }
        },
        mounted() {
            this.getTeams(1);

            // if there are curently no teams left in this 'page', load page 1.
            this.$watch(function () {
                    return this.teams.length < 1;
                }, function  (newVal, oldVal) {
                    this.getTeams(1);
            });

            eventHub.$on('AdminAddedTeams', this.getTeams);
        }
    }
</script>
