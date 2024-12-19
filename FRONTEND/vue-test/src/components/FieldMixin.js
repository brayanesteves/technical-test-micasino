import { toRaw } from "vue";

export default {
    setup(props, { emit }) {

        const handleChange = (event) => {
            let idAndValue = valueFromEvent(event, props.field.id);
            emit('update', idAndValue);
        };

        const valueFromEvent = (event, currentId) => {
            let value;
            let data;

            // if event comes from an html element.
            if (event.target) {

                value = event.target.value;
                // if checkbox
                if (event.target.type === 'checkbox') {
                    value = event.target.checked;
                }
                // if multiple select
                if (event.target.multiple) {
                    value = event.target.selectedOptions;
                }
            } else {
                // add to current value
                value = toRaw(props.modelValue);
                value[event.id] = event.value;
            }

            return {
                id: currentId,
                value: value,
            };

        };

        let timerId;
        const debounce = (callback, wait) => {
            return (...args) => {
                clearTimeout(timerId);
                timerId = setTimeout(() => {
                    callback(...args);
                }, wait);
            };
        };

        return {
            handleChange,
            valueFromEvent,
            debounce
        };
    },
};
