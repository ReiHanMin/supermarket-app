<template>
  <div>
   <div class="flex items-center justify-between mb-3">
    <h1 class="text-3xl font-semibold">Stores</h1>
    <!-- Add Store Button -->
    <router-link to="/app/stores/create" class="btn-primary">
      Add a Store
    </router-link>
  </div>

    <!-- StoreForm.vue -->
    <StoreForm v-if="showStoreForm" @close="showStoreForm = false" />

    <!-- List of stores goes here -->
    <StoresTable />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import StoreForm from './StoreForm.vue';  // Import the form
import StoresTable from './StoresTable.vue';  // Import stores listing

const showStoreForm = ref(false);  // Control the form display
</script>

<style scoped>
  .btn-primary {
    background-color: #4A90E2;
    color: white;
    padding: 8px 16px;
    border-radius: 4px;
    text-decoration: none;
  }

  .btn-primary:hover {
    background-color: #3d7bbd;
  }
</style>