<template>
    <div class="component">
        <scale-loader :loading="processing.submission" :color="loader_color"></scale-loader>

        <bootstrap-alert
            :expression="! processing.submission && success.submission"
            alert-type="success"
            message-bold="Success!"
        >
            <template slot="default">Round was created!</template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! processing.submission && (error.validation.date || error.validation.name)"
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
                We were unable to create the round at this moment!
                <br>
                Try again later.
            </template>
        </bootstrap-alert>

        <template v-if="! processing.submission">

                <h4>Create a Round</h4>
                <div class="form-group" :class="error.validation.name ? 'has-error' : ''">
                    <input type="text" v-model="round.name" id="name" placeholder="1" class="form-control">
                    <div v-if="error.validation.name" class="help-block danger">
                        You must enter a name no longer than 255 characters.
                    </div>
                </div>
                <div class="form-group" :class="error.validation.date ? 'has-error' : ''">
                    <input type="date" v-model="round.date" name="date" class="form-control" pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}" required>
                    <div v-if="error.validation.date" class="help-block danger">
                        You must enter a valid date.
                    </div>
                </div>

                <div class="form-group pull-right">
                    <button @click.prevent="addRound" :disabled="processing.submission" class="btn btn-default pull-right">Create Round</button>
                </div>
        </template>
    </div>
</template>

<script>
    import eventHub from '../../../events.js';
    export default {
        data() {
            return {
                round: {
                    name: '',
                    date: ''
                },
                success: {
                    submission: false
                },
                error: {
                    validation: {
                        name: false,
                        date: false
                    },
                    other: false
                },
                processing: {
                    submission: false,
                },
                loader_color: '#0d0394'
            }
        },
        methods: {
            addRound() {
                this.success.submission = false;

                this.processing.submission = true;

                // clear errors, as not to display old errors again
                this.error.validation.name = false;
                this.error.validation.date = false;
                this.error.other = false;

                return this.$http.post('/admin/rounds/new', {
                    name: this.round.name,
                    date: this.round.date
                }).then((response) => {
                    // clear input
                    this.round.name = '';

                    this.success.submission = true;

                    this.error.validation.name = false;
                    this.error.validation.date = false;
                    this.error.other = false;

                    this.processing.submission = false;

                    eventHub.$emit('AdminAddedRounds', 1);
                }, (response) => {
                    // clear input
                    this.round.name = '';

                    if (response.data.name) {
                        this.error.validation.name = true;
                    } else if (response.data.date) {
                        this.error.validation.date = true;
                    } else {
                        this.error.other = true;
                    }

                    this.processing.submission = false;
                });
            }
        }
    }
</script>
