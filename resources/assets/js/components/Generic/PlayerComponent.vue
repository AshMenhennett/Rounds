<template>
    <li class="list-group-item text-center" :class="{ 'temp-player' : player.temp }">
        <h4>{{ player.name }} <small>{{ player.rounds }} round(s).</small></h4>
        <a :href="'/admin/players/' + player.id + '/edit?v=players'" class="text-info"><span class="glyphicon glyphicon-edit"></span></a>
        <span v-show="player.rounds === 0">
            &nbsp;
            <a href="#" @click.prevent="destroy(index)" class="text-danger"><span class="glyphicon glyphicon-trash"></span></a>
        </span>
    </li>
</template>


<script>
    export default {
<<<<<<< HEAD
=======
        data () {
            return {
                players: [],
                meta: null
            }
        },
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
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
            isFor: {
                type: String,
                default: 'default'
            }
        },
        methods: {
            destroy(index) {
                this.$emit(this.isFor + 'DestroyedPlayer', index);
            }
        }
    }
</script>
