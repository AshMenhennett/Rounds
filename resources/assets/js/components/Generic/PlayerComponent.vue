<template>
    <li class="list-group-item text-center" :class="{ 'temp-player' : player.temp }">
        <h4>{{ player.name }} <small>{{ player.rounds }} round(s).</small></h4>
        <a :href="'/admin/players/' + player.id + '/edit'" class="text-info"><span class="glyphicon glyphicon-pencil"></span></a>
        <span v-show="player.rounds === 0">
            &nbsp;
            <a href="#" @click.prevent="destroy(index)" class="text-danger"><span class="glyphicon glyphicon-trash"></span></a>
        </span>
    </li>
</template>


<script>
    export default {
        data () {
            return {
                players: [],
                meta: null
            }
        },
        props: {
            player: {
                id: Number,
                name: String,
                rounds: Number,
                temp: Boolean
            },
            index: {
                type: Number,
                default: -1
            },
            for: {
                type: String,
                default: 'default'
            }
        },
        methods: {
            destroy(index) {
                this.$emit(this.for + 'DestroyedPlayer', index);
            }
        }
    }
</script>
