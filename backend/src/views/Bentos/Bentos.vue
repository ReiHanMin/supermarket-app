<template>
  <div class="flex items-center justify-between mb-3">
    <h1 class="text-3xl font-semibold">Bentos</h1>
    <router-link
      :to="{ name: 'app.bentos.create' }"
      class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
      Add new Bento
    </router-link>
  </div>
  <BentosTable />
</template>

<script setup>
import { computed } from 'vue';
import store from '../../store';
import BentosTable from './BentosTable.vue';

const bentos = computed(() => store.state.bentos);
</script>

<style scoped></style>
