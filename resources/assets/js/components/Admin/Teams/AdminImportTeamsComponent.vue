<template>
    <div class="component">

        <scale-loader :loading="importing" :color="loader_color"></scale-loader>

        <bootstrap-alert
            :expression="! importing && success.valid_data && ! warning.invalid_data"
            alert-type="success"
            message-bold="Success!"
        >
            <template slot="default">Teams imported!</template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! importing && success.valid_data && warning.invalid_data"
            alert-type="warning"
            message-bold="Almost!"
        >
            <template slot="default">Your teams were imported, except:</template>
            <template slot="extra">
                <ul>
                    <li v-for="team in warning.invalid_teams">{{ team.name }}</li>
                </ul>
                <p><strong>These teams may already exist.</strong></p>
            </template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! importing && ! success.valid_data && warning.invalid_data"
            alert-type="warning"
            message-bold="Warning!"
        >
            <template slot="default">
                Teams unable to be imported!
                <br>
                Please make sure these teams don't already exist before attempting to import again.
            </template>
        </bootstrap-alert>

        <bootstrap-alert
            :expression="! importing && (error.validation || error.empty_file || error.bad_date_present)"
            alert-type="danger"
            message-bold="Error!"
        >
            <template slot="default">
                Teams unable to be imported!
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
                Teams unable to be imported!
                <br>
                Please try again later.
            </template>
        </bootstrap-alert>

        <template v-if="! importing">
            <h4>Import Teams</h4>
            <div class="form-group" :class="(error.validation || error.empty_file) ? 'has-error' : ''">
                <input type="file" @change="importTeams($event)" id="file" class="form-control">
                <div v-if="error.validation" class="help-block danger">
                    You need to select a valid excel file with the correct schema for this type of import operation.
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
                    invalid_data: false,
                    invalid_teams: []
                },
                success: {
                    valid_data: false
                },
                loader_color: '#0d0394'
            }
        },
        methods: {
            importTeams(e) {
                this.importing = true;

                // clear errors
                this.warning.invalid_teams = [];
                this.warning.invalid_data = false;
                this.error.validation = false;
                this.error.empty_file = false;
                this.error.bad_date_present = false;
                this.error.other = false;

                // clear success
                this.success.valid_data = false;

                var formData = new FormData();
                formData.append('teams', e.target.files[0]);

                return this.$http.post('/admin/teams/import', formData).then((response) => {
                    if (response.body.invalid_team_data.length && response.body.valid_team_data.length) {
                        this.warning.invalid_teams = response.body.invalid_team_data;
                        this.warning.invalid_data = true;
                        this.success.valid_data = true;
                    } else if (! response.body.invalid_team_data.length && response.body.valid_team_data.length){
                        this.success.valid_data = true;
                    } else if (response.body.invalid_team_data.length && ! response.body.valid_team_data.length){
                        this.warning.invalid_teams = response.body.invalid_team_data;
                        this.warning.invalid_data = true;
                    }
                    this.importing = false;

                    eventHub.$emit('AdminAddedTeams', 1);
                }, (response) => {
                    if (response.data.teams) {
                        // validation error
                        this.error.validation = true;
                    } else if (response.status == 400) {
                        // file is considered empty (eg. wrong format)
                        this.error.empty_file = true;
                    } else if (response.status == 406) {
                        // an invalid date was present in uploaded file
                        this.error.bad_date_present = true;
                    } else {
                        // some other error
                        this.error.other = true;
                    }
                    this.importing = false;
                });
            }
        }
    }
</script>
