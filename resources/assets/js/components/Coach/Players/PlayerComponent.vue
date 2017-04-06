<template>
    <li class="list-group-item text-center" :class="{ 'temp-player' : player.temp }">
        <h4>{{ player.name }} <small>{{ player.rounds }} round(s).</small></h4>
        <a :href="'/teams/' + team + '/players/' + player.id + '/edit'" class="text-info"><span class="glyphicon glyphicon-edit"></span></a>
        <span v-show="player.rounds === 0">
            &nbsp;
            <a href="#" @click.prevent="destroy(index)" class="text-danger"><span class="glyphicon glyphicon-trash"></span></a>
        </span>
    </li>
</template>


<script>
    export default {
        props: {
            player: {
                id: Number,
                name: String,
                rounds: Number,
                temp: Boolean
            },
            team: {
                type: String,
                default: ''
            },
            type: {
                type: String,
                default: ''
            },
            index: {
                type: Number,
                default: -1
            },
            isFor: {
                type: String,
                default: 'default'
            }
        },
        methods: {
            destroy(index) {
                if (! confirm("Are you sure you want to delete this player?")) {
                    return;
                }
                this.$emit(this.isFor + 'Destroyed' + this.type + 'Player', index);
            }
        }
    }
</script>
