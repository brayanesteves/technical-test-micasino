<template>
    <div class="dynamic-form">
      <div class="header">
        <img src="../assets/micasino.png" alt="Logo" class="logo" />
      </div>
      <form @submit.prevent="onSubmit" class="form-group">
        <div class="form-field">
          <label for="name">Nombre</label>
          <input
            id="name"
            v-model="formData.name"
            type="text"
            class="input"
            placeholder="Nombre-ejemplo"
            :class="{ 'input-error': errors.name }"
          />
          <span v-if="errors.name" class="error-message">{{ errors.name }}</span>
        </div>
        <SelectUnselectField
          :options="availableOptions"
          @onOptionsChange="updateAvailableOptions"
        />
        <span v-if="errors.options" class="error-message">{{ errors.options }}</span>
        <div class="button-container">
          <button type="submit" class="button primary">Guardar</button>
        </div>
      </form>
      <div class="result">
        <label for="result">Result:</label>
        <textarea id="result" rows="5" v-model="result" readonly></textarea>
      </div>
    </div>
  </template>
  
  <script setup>
  import { reactive, ref } from 'vue';
  import SelectUnselectField from './SelectUnselectField.vue';
  
  const formData = reactive({
    name: '',
    availableOptions: [],
  });
  
  const availableOptions = ref([
    { id: '1', label: 'Option 1', disabled: false },
    { id: '2', label: 'Option 2', disabled: false },
    { id: '3', label: 'Option 3', disabled: false },
  ]);
  
  const result = ref('');
  const errors = reactive({
    name: '',
    options: '',
  });
  
  const updateAvailableOptions = (options) => {
    formData.availableOptions = options.filter(option => !option.disabled).map(option => option.id);
  };
  
  const validateForm = () => {
    errors.name = '';
    errors.options = '';
  
    if (!formData.name) {
      errors.name = 'El nombre es requerido.';
    }
  
    return !errors.name && !errors.options;
  };
  
  const onSubmit = () => {
    if (validateForm()) {
      const selectedOptions = availableOptions.value.filter(option => !option.disabled);
      result.value = JSON.stringify({
        name: formData.name,
        options: selectedOptions.map(option => option.label),
      }, null, 2);
    }
  };
  </script>
  
  <style scoped>
  .dynamic-form {
    background-color: #222;
    color: #fff;
    padding: 20px;
    border-radius: 8px;
    width: 400px;
    margin: auto;
    font-family: Arial, sans-serif;
  }
  
  .header {
    text-align: center;
    margin-bottom: 20px;
  }
  
  .logo {
    max-width: 200px;
  }
  
  .form-group {
    display: flex;
    flex-direction: column;
    gap: 15px;
  }
  
  .form-field {
    display: flex;
    flex-direction: column;
  }
  
  label {
    font-size: 14px;
    margin-bottom: 5px;
  }
  
  .input {
    padding: 8px;
    border: 1px solid #444;
    border-radius: 4px;
    background-color: #333;
    color: #fff;
  }
  
  .input-error {
    border-color: red;
  }
  
  .error-message {
    color: red;
    font-size: 12px;
  }
  
  .button-container {
    text-align: center;
    margin-top: 20px;
  }
  
  .button {
    background-color: #ffcc00;
    color: #222;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
  }
  
  .result {
    margin-top: 20px;
  }
  
  textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #444;
    border-radius: 4px;
    background-color: #333;
    color: #fff;
    font-family: monospace;
  }
  </style>