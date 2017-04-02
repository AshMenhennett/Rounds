<template>
    <li class="list-group-item text-center">
        <h4>{{ button.value }}</h4>
        <span v-show="button.file_name != null">
            <span @click.prevent="button.sendToUrlForInternalFile()" class="text-info" style="cursor:pointer;"><span class="glyphicon glyphicon-file"></span></span>
        </span>
        <span v-show="button.file_name == null">
            <a :href="button.link" target="_blank" class="text-info"><span class="glyphicon glyphicon-new-window"></span></a>
        </span>

        <a href="#" @click.prevent="destroy(index)" class="text-danger" style="cursor:pointer;"><span class="glyphicon glyphicon-remove"></span></a>
    </li>
</template>

<script>
    export default {
        props: {
            button: {
                id: Number,
                value: String,
                link: null,
                file_name: null
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
                if (! confirm("Are you sure you want to delete this button?")) {
                    return;
                }
                this.$emit(this.isFor + 'DestroyedEcosystemButton', index);
            }
        }
    }
</script>
