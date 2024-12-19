<template>
    <input
        :type="field.type || 'text'"
        :name="field.id"
        :disabled="field.disabled ?? null"
        :value="modelValue"
        @keyup="updateData"
    />
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
let { handleChange, debounce } = fieldMixin.setup(props, { emit });
const updateData = (event) => {
    debounce(handleChange, 50)(event);
};

</script>
<style lang="css" scoped>
input {
    background: black;
    border-width: 1px;
    padding: 10px;
}
</style>
