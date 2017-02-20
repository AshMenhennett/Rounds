<template>
    <li class="list-group-item text-center">
<<<<<<< HEAD
        <h4>
            <a v-if="team.hasCoach" :href="'/teams/' + team.slug + '/manage'">{{ team.name }} <small>{{ team.players }} player(s).</small></a>
            <span v-if="! team.hasCoach">{{ team.name }} <small>{{ team.players }} player(s).</small></span>
        </h4>
=======
        <h4>{{ team.name }} <small>{{ team.players }} player(s).</small></h4>
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
        <a :href="'/admin/teams/' + team.slug + '/edit?v=teams'" class="text-info"><span class="glyphicon glyphicon-edit"></span></a>
        <span v-show="team.players === 0">
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
                teams: [],
                meta: null
            }
        },
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
        props: {
            team: {
                id: Number,
                name: String,
                slug: String,
<<<<<<< HEAD
                hasCoach: Boolean,
=======
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
                players: Number
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
                this.$emit(this.isFor + 'DestroyedTeam', index);
            }
        }
    }
</script>
