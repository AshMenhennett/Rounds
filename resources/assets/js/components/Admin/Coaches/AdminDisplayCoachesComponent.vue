<template>
    <div class="component">
        <scale-loader :loading="loading" :color="loader_color"></scale-loader>

        <template v-if="! loading">
            <pages v-if="meta && coaches.length" :pagination="meta.pagination" isFor="adminCoaches" @adminCoachesChangedPage="getCoaches"></pages>

            <ul class="list-group">
                <coach v-for="(coach, index) in coaches" :coach="coach" :index="index" isFor="admin" @adminDestroyedCoach="destroyCoach"></coach>
            </ul>

            <pages v-if="meta && coaches.length" :pagination="meta.pagination" isFor="adminCoaches" @adminCoachesChangedPage="getCoaches"></pages>

            <bootstrap-alert
                :expression="no_coaches && ! loading"
                alert-type="info"
                message-bold="Uh oh!"
            >
                <template slot="default">
                    There are currently no coaches!
                    <br>
                    Invite coaches to <a href="/register" class="alert-link">sign up</a>.
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
                coaches: [],
                meta: null,
                loading: false,
                no_coaches: false,
                loader_color: '#0d0394'
            }
        },
        methods: {
            getCoaches(page) {
                this.loading = true;
                return this.$http.get('/admin/coaches/fetch?page=' + page).then((response) => {
                    this.coaches = response.body.data;
                    this.meta = response.body.meta;

                    this.loading = false;

                    if (! this.coaches.length) {
                        this.no_coaches = true;
                    } else {
                        this.no_coaches = false;
                    }
                });
            },
            destroyCoach(index) {
                var id = this.coaches[index].id;

                this.coaches.splice(index, 1);

                return this.$http.delete('/admin/coaches/' + id);
            }
        },
        mounted() {
            this.getCoaches(1);

            // if there are curently no coaches left in this 'page', load page 1.
            this.$watch(function () {
                    return this.coaches.length < 1;
                }, function  (newVal, oldVal) {
                    this.getCoaches(1);
            });
        }
    }
</script>
