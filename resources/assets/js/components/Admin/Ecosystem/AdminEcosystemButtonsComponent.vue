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
            <li v-for="(button, index) in buttons" class="list-group-item text-center">
                <h4>{{ button.value }}</h4>
                <span v-show="button.file_name != null">
                    <span @click.prevent="button.sendToUrlForInternalFile()" class="text-info" style="cursor:pointer;"><span class="glyphicon glyphicon-file"></span></span>
                </span>
                <span v-show="button.file_name == null">
                    <a :href="button.link" target="_blank" class="text-info"><span class="glyphicon glyphicon-new-window"></span></a>
                </span>

                <a href="#" @click.prevent="destroy(button.id, index)" class="text-danger" style="cursor:pointer;"><span class="glyphicon glyphicon-remove"></span></a>
            </li>
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
            destroy(id, index) {
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
