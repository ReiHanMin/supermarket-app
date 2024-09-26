<template>
  <div>
    <!-- Show spinner while loading -->
    <Spinner v-if="stores.loading" />

    <!-- Show table when stores data is loaded -->
    <div v-else class="bg-white p-4 rounded-lg shadow">
      <div class="flex justify-between border-b-2 pb-3">
        <div class="flex items-center">
          <span class="whitespace-nowrap mr-3">Per Page</span>
          <select v-model="perPage" @change="getStores" class="w-24 px-3 py-2 border">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="20">20</option>
          </select>
          <span class="ml-3">Found {{ stores.total }} stores</span>
        </div>
        <input v-model="search" @input="getStores" placeholder="Search stores" class="border px-3 py-2 rounded-lg">
      </div>

      <table class="table-auto w-full mt-4">
        <thead>
          <tr class="bg-gray-100 text-left">
            <th class="p-3">ID</th>
            <th class="p-3">Photo</th>
            <th class="p-3">Name</th>
            <th class="p-3">Chain Name</th>
            <th class="p-3">Address</th>
            <th class="p-3">Email</th>
            <th class="p-3">Phone</th>
            <th class="p-3">Updated At</th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="store in stores.data" :key="store.id">
            <td class="p-3">{{ store.id }}</td>
            <td class="p-3">
              <img :src="getPhotoUrl(store.photo)" alt="Store photo" class="h-12 w-12 object-cover rounded-md" v-if="store.photo"/>
              <span v-else>No Photo Available</span>
            </td>
            <td class="p-3">{{ store.name }}</td>
            <td class="p-3">{{ store.chain_name }}</td>
            <td class="p-3">{{ store.address }}</td>
            <td class="p-3">{{ store.email }}</td>
            <td class="p-3">{{ store.phone }}</td>
            
            <td class="p-3">{{ formatDate(store.updated_at) }}</td>
           <td class="p-3">
            <div class="flex space-x-2">
              <button
                @click="editStore(store)"
                class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600"
              >
                Edit
              </button>
              <button
                :disabled="deletingStore"
                @click="deleteStore(store.id)"
                class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600"
              >
                Delete
              </button>
            </div>
          </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import store from '../../store';
import Spinner from '../../components/core/Spinner.vue';
import { useRouter } from 'vue-router'; // To navigate to the edit page

const perPage = ref(10);
const search = ref('');
const sortField = ref('name');
const sortDirection = ref('asc');
const stores = computed(() => store.state.stores);
const router = useRouter(); // Initialize the router to navigate to other pages
const deletingStore = ref(false);

onMounted(() => {
  getStores(); // Fetch the stores when the component is mounted
});

function getStores() {
  store.dispatch('getStores', {
    search: search.value,
    per_page: perPage.value,
    sort_field: sortField.value,
    sort_direction: sortDirection.value,
  });
}

function deleteStore(storeId) {
  if (confirm('Are you sure you want to delete this store?')) {
    store.dispatch('deleteStore', storeId)
      .then(() => {
        store.commit('showToast', 'Store successfully deleted');
        getStores(); // Fetch the updated list of stores after deletion
      })
      .catch((error) => {
        console.error('Error deleting store:', error);
        store.commit('showToast', 'Error deleting store');
      });
  }
}

function editStore(store) {
  router.push({ name: 'edit-store', params: { id: store.id } });  // Push the route with the store id as a param
}

// Helper function to format the date
function formatDate(dateString) {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
}

// Helper function to build the photo URL
function getPhotoUrl(photoPath) {
  return photoPath ? `http://localhost:8000${photoPath}` : null;
}

</script>

<style scoped>
/* Add any specific styles here */
</style>
