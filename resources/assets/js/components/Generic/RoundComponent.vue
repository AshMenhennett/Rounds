<template>
    <li class="list-group-item text-center">
        <h4>{{ round.name }} <small>{{ round.default_date }}</small></h4>
        <a :href="'/admin/rounds/' + round.id + '/edit?v=rounds'" class="text-info"><span class="glyphicon glyphicon-edit"></span></a>
        <span v-show="round.teams === 0">
            &nbsp;
            <a href="#" @click.prevent="destroy(index)" class="text-danger"><span class="glyphicon glyphicon-trash"></span></a>
        </span>
    </li>
</template>

<script>
    export default {
        props: {
            round: {
                id: Number,
                name: String,
                default_date: String,
                email: String,
                teams: Number
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
                this.$emit(this.isFor + 'DestroyedRound', index);
            }
        }
    }
</script>
