<template>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <div class="panel panel-default">
                <div class="panel-heading">Clear System</div>
                <div class="panel-body text-center">
                    <template>
                        <div class="modal" id="deleteAllModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">DESTROY SYSTEM</h4>
                                    </div>
                                    <div class="modal-body">
                                        <p>To complete the deletion process, type 'DELETE' into the input below and click the Delete button.</p>
                                        <div class="form-group" :class="error.input ? 'has-error' : ''">
                                            <input v-model="input" type="text" class="form-control">
                                            <div v-if="error.input" class="help-block danger">
                                                You did not enter the required text.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                        <button type="button" @click.prevent="deleteAll" class="btn btn-danger">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <bootstrap-alert
                        :expression="success"
                        alert-type="success"
                        message-bold="Success!"
                    >
                        <template slot="default">You have cleared out the system. We hope you had a great Season!</template>
                    </bootstrap-alert>

                    <p>Clicking this button will initiate the processing of clearing out the entire system, preparing it for a fresh start next season.</p>
                    <button @click.prevent="triggerModal" class="btn btn-danger btn-xl">DELETE ALL</button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                input: null,
                error: {
                    input: false,
                    other: false
                },
                success: false
            }
        },
        methods: {
            triggerModal() {
                $('#deleteAllModal').modal();
            },
            deleteAll() {
                this.success = false;
                this.error.input = false;
                this.error.other = false;

                if (this.input != 'DELETE') {
                    this.error.input = true;
                    return;
                }

                return this.$http.delete('/admin/delete').then((response) => {
                    this.success = true;
                    $('#deleteAllModal').modal('hide');
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                }, (response) => {
                    this.error.other = true;
                });
            }
        }
    }
</script>
