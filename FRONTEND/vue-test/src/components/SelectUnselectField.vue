<template>
    <div class="select-unselect-field">
      <div class="options-list">
        <label>Available Options</label>
        <ul>
          <li v-for="option in options" :key="option.id">
            <label>
              <input
                type="checkbox"
                :value="option.id"
                :checked="!option.disabled"
                @change="toggleOption(option)"
              />
              {{ option.label }}
            </label>
          </li>
        </ul>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps } from 'vue';
  
  const props = defineProps({
    options: Array,
    onOptionsChange: Function,
  });
  
  const toggleOption = (option) => {
    option.disabled = !option.disabled;  // Cambia el estado de la opción
    props.onOptionsChange(props.options); // Notifica al componente padre
  };
  </script>
  
  <style scoped>
  .select-unselect-field {
    margin-top: 20px;
  }
  
  .options-list {
    display: flex;
    flex-direction: column;
  }
  </style>