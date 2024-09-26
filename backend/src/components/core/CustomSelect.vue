<template>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">{{ label }}</label>
    <select
      :value="modelValue" 
      @change="$emit('update:modelValue', $event.target.value)"  
      class="appearance-none block w-full px-3 py-2 border border-gray-300 bg-white text-gray-900 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
    >
      <option v-for="option in options" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>
    <p v-if="errors" class="mt-1 text-sm text-red-600">{{ errors[0] }}</p>
  </div>
</template>

<script setup>
defineProps({
  modelValue: String,   
  options: Array,       
  label: String,        
  errors: Array         
});
</script>

<style scoped>
/* Add any custom styles here */
</style>
