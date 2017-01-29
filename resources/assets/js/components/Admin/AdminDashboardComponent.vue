<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <div class="panel panel-default">
                    <div class="panel-heading">Admin Dashboard</div>
                    <div class="panel-body">
                        <ul class="nav nav-tabs">
                            <li role="presentation"><a href="#" @click.prevent="changeView('admin-stats')">Stats</a></li>
                            <li role="presentation"><a href="#" @click.prevent="changeView('admin-players-master')">Players</a></li>
                            <!-- <li role="presentation"><a href="#" @click.prevent="changeView('admin-coaches')">Coaches</a></li>
                            <li role="presentation"><a href="#" @click.prevent="changeView('admin-teams')">Teams</a></li>
                            <li role="presentation"><a href="#" @click.prevent="changeView('admin-rounds')">Rounds</a></li> -->
                        </ul>

                        <keep-alive>
                            <component :is="currentView"></component>
                        </keep-alive>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                currentView: 'admin-stats'
            }
        },
        methods: {
            changeView(component) {
                this.currentView = component;
            }
        }
    }
</script>
