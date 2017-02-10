<template>
    <li class="list-group-item text-center">
        <h4>{{ team.name }} <small>{{ team.players }} player(s).</small></h4>
        <a :href="'/admin/teams/' + team.slug + '/edit?v=teams'" class="text-info"><span class="glyphicon glyphicon-edit"></span></a>
        <span v-show="team.players === 0">
            &nbsp;
            <a href="#" @click.prevent="destroy(index)" class="text-danger"><span class="glyphicon glyphicon-trash"></span></a>
        </span>
    </li>
</template>


<script>
    export default {
        data () {
            return {
                teams: [],
                meta: null
            }
        },
        props: {
            team: {
                id: Number,
                name: String,
                slug: String,
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
