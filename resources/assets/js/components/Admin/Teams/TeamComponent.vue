<template>
    <li class="list-group-item text-center">
        <h4>
            <a :href="'/teams/' + team.slug + '/manage'">{{ team.name }} <small>{{ team.players }} player(s).</small></a>
        </h4>
        <a :href="'/admin/teams/' + team.slug + '/edit?v=teams'" class="text-info"><span class="glyphicon glyphicon-edit"></span></a>
        <span v-show="team.players === 0">
            &nbsp;
            <a href="#" @click.prevent="destroy(index)" class="text-danger"><span class="glyphicon glyphicon-trash"></span></a>
        </span>
    </li>
</template>


<script>
    export default {

        props: {
            team: {
                id: Number,
                name: String,
                slug: String,
                hasCoach: Boolean,
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
                if (! confirm("Are you sure you want to delete this team?")) {
                    return;
                }
                this.$emit(this.isFor + 'DestroyedTeam', index);
            }
        }
    }
</script>
