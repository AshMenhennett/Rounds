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
                    <a :href="'https://s3-ap-southeast-2.amazonaws.com/files.smaa-ch.herokuapp/files/' + button.file_name" target="_blank" class="text-info"><span class="glyphicon glyphicon-file"></span></a>
                </span>
                <span v-show="button.file_name == null">
                    <a :href="button.link" target="_blank" class="text-info"><span class="glyphicon glyphicon-new-window"></span></a>
                </span>

                <a href="#" @click.prevent="destroy(button.id, index)" class="text-danger"><span class="glyphicon glyphicon-remove"></span></a>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                buttons: []
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
        }
    }
</script>
