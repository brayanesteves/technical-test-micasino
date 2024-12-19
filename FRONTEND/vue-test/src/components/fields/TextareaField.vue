<template>
    <textarea 
        :id="field.id" 
        @change="handleChange"
        :value="modelValue"
    ></textarea>
</template>

<script setup>
import fieldMixin from '../FieldMixin';
const emit = defineEmits(['update']);
const props = defineProps({
    field: {
        type: Object,
        required: true,
    },
    modelValue: {
        type: [String, Number, Boolean, Object, Array],
        default: '',
    },
});
let { handleChange } = fieldMixin.setup(props, { emit });

</script>

<style scoped>
/* Add your styles here */
</style>