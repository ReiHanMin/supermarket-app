<template>
  <div class="mb-4">
    <label for="store-select" class="block text-sm font-medium text-gray-700 mb-1">Select a Store</label>
    
    <!-- Display a loading spinner or message while the stores are loading -->
    <div v-if="loading" class="text-gray-500">Loading stores...</div>
    
    <select 
      v-else
      id="store-select" 
      v-model="selectedStore" 
      @change="onStoreChange" 
      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
    >
      <option disabled value="">-- Select a store --</option>
      <option v-for="store in stores" :key="store.id" :value="store.id">
        {{ store.name }}
      </option>
    </select>
  </div>
</template>

<script>
export default {
  data() {
    return {
      selectedStore: ''
    };
  },
  computed: {
    stores() {
      return this.$store.state.stores.data;
    },
    loading() {
      return this.$store.state.stores.loading;
    }
  },
  methods: {
    onStoreChange() {
      if (this.selectedStore) {
        this.$emit('storeSelected', this.selectedStore);
      }
    }
  },
  created() {
    if (!this.stores.length) {
      this.$store.dispatch('getStores');
    }
  }
};
</script>
