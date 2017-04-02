<template>
    <div class="component">

        <bootstrap-alert
                :expression="! buttons.length"
                alert-type="info"
                message-bold="Uh oh!"
            >
                <template slot="default">
                    There are currently no buttons!
                    <br>
                    Add some with the fields above.
                </template>
            </bootstrap-alert>

        <ul class="list-item-group">
            <ecosystem-button v-for="(button, index) in buttons" :button="button" :index="index" isFor="admin" @adminDestroyedEcosystemButton="destroy"></ecosystem-button>
        </ul>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                buttons: [],
                s3_files_bucket_url: window.App.s3_files_bucket_url
            }
        },
        props: {
            buttonsProp: null
        },
        methods: {
            destroy(index) {
                var id = this.buttons[index].id;
                this.buttons.splice(index, 1);
                return this.$http.delete('/admin/ecosystem/buttons/' + id);
            }
        },
        mounted() {
            this.buttons = JSON.parse(this.buttonsProp);
            var that = this;
            this.buttons.forEach(function (e, i, a) {
                e.getFileExtension = function () {
                    return this.file_name.substr(e.file_name.lastIndexOf('.') + 1);
                };
                e.hasPDFFile = function () {
                    return this.getFileExtension(this) === 'pdf';
                };
                e.sendToUrlForInternalFile = function () {
                    if (this.hasPDFFile()) {
                        return window.open('/view/files/pdf/' + this.file_name, '_blank');
                    }
                    return window.open(that.s3_files_bucket_url + this.file_name, '_blank');
                };
            });
        }
    }
</script>
