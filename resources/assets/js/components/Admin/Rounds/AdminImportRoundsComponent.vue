<template>
    <div class="component">

        <scale-loader :loading="importing" :color="loader_color"></scale-loader>

        <bootstrap-alert
            :expression="! importing && success.import"
            alert-type="success"
            message-bold="Success!"
        >
            <template slot="default">Rounds imported!</template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! importing && (error.validation || error.empty_file || error.bad_date_present)"
            alert-type="danger"
            message-bold="Error!"
        >
            <template slot="default">
                Rounds unable to be imported!
                <br>
                Please check the file is a valid Excel file.
                <br>
                <a href="/faq#valid-excel" target="_blank" class="alert-link">Example of valid Excel format <span class="glyphicon glyphicon-new-window"></span></a>.
            </template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! importing && error.other"
            alert-type="danger"
            message-bold="Error!"
        >
            <template slot="default">
                Rounds unable to be imported!
                <br>
                Please try again later.
            </template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! importing && warning.invalid_data_submitted"
            alert-type="warning"
            message-bold="Warning!"
        >
            <template slot="default">
                Some rounds were unable to be imported!
                <br>
                Please complete the following entries.
                <template v-if="warning.invalid_fixed_data_submitted">
                    <br>
                    <strong>Please fix your data for submission</strong>.
                </template>
            </template>
        </bootstrap-alert>

        <template v-if="! importing && warning.invalid_data_submitted">
            <div v-for="(round, index) in invalid_rounds">
                <div class="form-group col-no-pad col-md-6 has-error">
                    <label :for="'name-' + index">Name</label>
                    <input class="form-control" type="text" v-model="round.name" :id="'name-' + index" placeholder="1">
                    <div class="help-block danger">
                        You must enter a name no longer than 255 characters.
                    </div>
                </div>
                <div class="form-group col-md-6 has-error">
                    <label :for="'date-' + index">Date</label>
                    <input class="form-control" type="date" v-model="round.date" :id="'date-' + index">
                    <div class="help-block danger">
                        Please enter a date in the format: DD/MM/YYYY.
                    </div>
                </div>
            </div>
            <div class="form-group col-md-12">
                <a href="#" @click.prevent="submitFixedRounds" class="btn btn-primary pull-right">Submit</a>
            </div>
        </template>

        <template v-if="! importing && ! warning.invalid_data_submitted">
            <h4>Import Rounds</h4>
            <div class="form-group" :class="error.validation ? 'has-error' : ''">
                <input type="file" id="file" @change="importRounds($event)" class="form-control">
                <div v-if="error.validation" class="help-block danger">
                    You need to select a valid excel file.
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import eventHub from '../../../events.js';
    export default {
        data() {
            return {
                importing: false,
                error: {
                    validation: false,
                    empty_file: false,
                    bad_date_present: false,
                    other: false
                },
                warning: {
                    invalid_data_submitted: false,
                    invalid_fixed_data_submitted: false
                },
                success: {
                    import: false
                },
                invalid_rounds: [],
                loader_color: '#0d0394'
            }
        },
        methods: {
            importRounds(e) {
                this.importing = true;
                this.success.import = false;

                // reset errors
                this.error.validation = false;
                this.error.empty_file = false;
                this.error.bad_date_present = false;
                this.error.other = false;
                this.warning.invalid_data_submitted = false;

                var formData = new FormData();
                formData.append('rounds', e.target.files[0]);

                return this.$http.post('/admin/rounds/import', formData).then((response) => {
                    if (response.data.invalid_round_data.length || response.status != 200) {
                        this.invalid_rounds = response.data.invalid_round_data;
                        this.warning.invalid_data_submitted = true;
                    } else {
                        this.success.import = true;
                    }

                    this.importing = false;

                    eventHub.$emit('AdminAddedRounds', 1);
                }, (response) => {
                    if (response.data.name || response.data.date) {
                        // validation error
                        this.error.validation = true;
                    } else if (response.status == 400) {
                        // file is considered empty (eg. wrong format)
                        this.error.empty_file = true
                    } else if (response.status == 406) {
                        // an invalid date was present in uploaded file
                        this.error.bad_date_present = true;
                    } else {
                        // some other error
                        this.error.other = true;
                    }

                    this.importing = false;
                });
            },
            submitFixedRounds() {
                this.importing = true;

                // reset errors
                this.warning.invalid_fixed_data_submitted = false;

                return this.$http.post('/admin/rounds/import/fixed', {
                    rounds: this.invalid_rounds
                }).then((response) => {
                    if (response.data.invalid_round_data.length || response.status != 200) {
                        this.invalid_rounds = response.data.invalid_round_data;
                        this.warning.invalid_fixed_data_submitted = true;
                    } else {
                        this.success.import = true;

                        this.warning.invalid_data_submitted = false;
                    }
                    this.importing = false;

                    eventHub.$emit('AdminAddedRounds', 1);
                }, (response) => {
                    this.importing = false;
                });
            }
        }
    }
</script>
