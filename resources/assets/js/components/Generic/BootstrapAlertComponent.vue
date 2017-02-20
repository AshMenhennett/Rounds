<template>
    <div v-if="expression" class="alert" :class="'alert-' + alertType" :style="styles">
        <p><strong>{{ messageBold }}</strong> <slot name="default"></slot></p>
        <slot name="extra"></slot>
    </div>
</template>


<script>
    export default {
        props: {
            expression: {
                type: Boolean,
                default: false,
                required: true
            },
            alertType: {
                type: String,
                default: 'success',
                required: true
            },
            messageBold: {
                type: String,
                default: 'Success!',
                required: true
            },
            styles: {
                type: Object,
                default: function () {
                    return {
                        'text-align': 'center'
                    }
                }
            }
        }
    }
</script>
