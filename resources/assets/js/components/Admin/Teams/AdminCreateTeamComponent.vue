<template>
    <div class="component">
        <scale-loader :loading="processing.submission" :color="loader_color"></scale-loader>

        <bootstrap-alert
            :expression="! processing.submission && success.submission"
            alert-type="success"
            message-bold="Success!"
        >
            <template slot="default">Team was created!</template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! processing.submission && error.validation.name"
            alert-type="danger"
            message-bold="Error!"
        >
            <template slot="default">
                Some errors occured processing your submission!
                <br>
                Please check your input.
            </template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! processing.submission && error.other"
            alert-type="danger"
            message-bold="Error!"
        >
            <template slot="default">
                We were unable to create the team at this moment!
                <br>
                Try again later.
            </template>
        </bootstrap-alert>

        <template v-if="! processing.submission">
            <h4>Create a Team</h4>
            <div class="form-group" :class="error.validation.name ? 'has-error' : ''">
                <input @keyup.enter="createTeam" type="text" v-model="team.name" id="name" placeholder="The Bears" class="form-control">
                <div v-if="error.validation.name" class="help-block danger">
                    The team must have a unique name no longer than 255 characters.
                </div>
            </div>
            <div class="form-group pull-right">
                <button @click.prevent="createTeam" class="btn btn-default pull-right">Create Team</button>
            </div>
        </template>
    </div>
</template>

<script>
    import eventHub from '../../../events.js';
    export default {
        data() {
            return {
                team: {
                    name: '',
                },
                success: {
                    submission: false
                },
                error: {
                    validation: {
                        name: false
                    },
                    other: false
                },
                processing: {
                    submission: false
                },
                loader_color: '#0d0394'
            }
        },
        methods: {
            createTeam() {
                this.processing.submission = true;

                // clear errors
                this.error.validation.name = false;
                this.error.other = false;

                // clear success
                this.success.submission = false;

                return this.$http.post('/admin/teams/new', {
                    name: this.team.name,
                }).then((response) => {
                    this.success.submission = true;
                    this.processing.submission = false;

                    this.team.name = '';

                    eventHub.$emit('AdminAddedTeams', 1);
                }, (response) => {

                     if (response.data.name) {
                        this.error.validation.name = true;
                    } else {
                        this.error.other = true;
                    }

                    this.processing.submission = false;
                });
            }
        }
    }
</script>
