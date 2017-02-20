<template>
    <li class="list-group-item text-center">
        <h4>{{ coach.first_name }} {{ coach.last_name }} <small>{{ coach.team.name && coach.team.name ?  coach.team.name : '' }}</small></h4>
        <a :href="'mailto:' + coach.email">Send Email</a>
        <span v-show="coach.role != 'admin'">
            <br />
            <a href="#" @click.prevent="destroy(index)" class="text-danger"><span class="glyphicon glyphicon-trash"></span></a>
        </span>
    </li>
</template>

<script>
    export default {
        props: {
            coach: {
                id: Number,
                first_name: String,
                last_name: String,
                email: String,
                role: String,
                team: {
                    name: String,
                    slug: String
                }
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
                this.$emit(this.isFor + 'DestroyedCoach', index);
            }
        }
    }
</script>
