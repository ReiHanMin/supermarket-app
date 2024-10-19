<template>
  <div class="bg-white p-4 rounded-lg shadow animate-fade-in-down">
    <!-- Store Selection -->
    <StoreSelect @storeSelected="fetchBentosForStore" />

    <!-- Date/Time Picker for Visit Time -->
    <div class="mt-4">
      <label for="visit-time" class="block text-sm font-medium text-gray-700 mb-1">Visit Date/Time</label>
      <input type="datetime-local" v-model="visitTime" id="visit-time"
             class="mt-1 block w-full pl-3 pr-10 py-2 border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
    </div>

    <!-- Bento Table Controls (Per Page & Search) -->
    <div class="flex justify-between border-b-2 pb-3 mt-4">
      <div class="flex items-center">
        <span class="whitespace-nowrap mr-3">Per Page</span>
        <select v-model="perPage" @change="fetchBentos"
                class="appearance-none relative block w-24 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
        <span class="ml-3">Found {{ bentos.total }} bentos</span>
      </div>
      <div>
        <input v-model="search" @change="fetchBentos"
               class="appearance-none relative block w-48 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
               placeholder="Type to Search bentos">
      </div>
    </div>

    <!-- Bento Data Table -->
    <div class="table-wrapper">
    <table class="table-auto w-full mt-4">
      <thead>
        <tr>
          <TableHeaderCell field="id" :sort-field="sortField" :sort-direction="sortDirection" @click="sortBentos('id')">ID</TableHeaderCell>
          <TableHeaderCell field="image">Image</TableHeaderCell>
          <TableHeaderCell field="name" :sort-field="sortField" :sort-direction="sortDirection" @click="sortBentos('name')">Bento Name</TableHeaderCell>
          <TableHeaderCell field="original_price" :sort-field="sortField" :sort-direction="sortDirection" @click="sortBentos('original_price')">Original Price (¥)</TableHeaderCell>
          <TableHeaderCell field="discount_percentage" :sort-field="sortField" :sort-direction="sortDirection" @click="sortBentos('discount_percentage')">Discount Percentage (%)</TableHeaderCell>
          <TableHeaderCell field="usual_discounted_price" :sort-field="sortField" :sort-direction="sortDirection" @click="sortBentos('usual_discounted_price')">Discounted Price (¥)</TableHeaderCell>
          <TableHeaderCell field="availability" :sort-field="sortField" :sort-direction="sortDirection" @click="sortBentos('availability')">Availability</TableHeaderCell>
          <TableHeaderCell field="store_name" :sort-field="sortField" :sort-direction="sortDirection" @click="sortBentos('store_name')">Store Name</TableHeaderCell>
          <TableHeaderCell field="actions">Actions</TableHeaderCell>
        </tr>
      </thead>

      <!-- Loading Spinner -->
      <tbody v-if="bentos.loading">
        <tr>
          <td colspan="10" class="text-center py-8">
            <Spinner />
            Loading bentos...
          </td>
        </tr>
      </tbody>

      <!-- Bento Data Rows -->
      <tbody v-else-if="bentos.data.length">
        <tr v-for="(bento, index) of bentos.data" :key="bento.id">
          <td class="border-b p-2">{{ bento.id }}</td>
          <td class="border-b p-2">
            <img v-if="bento.image_url" class="w-16 h-16 object-cover" :src="getPhotoUrl(bento.image_url)" :alt="bento.name" />
            <img v-else class="w-16 h-16 object-cover" src="../../assets/noimage.png" />
          </td>
          <td class="border-b p-2">{{ bento.name }}</td>
          <td class="border-b p-2">{{ formatCurrency(bento.original_price) }}</td>
          <td class="border-b p-2">
            <input type="number" v-model="bento.discount_percentage" class="w-full px-2 py-1 border rounded" />
          </td>
          <td class="border-b p-2">
            <input type="number" v-model="bento.usual_discounted_price" class="w-full px-2 py-1 border rounded" />
          </td>
          <td class="border-b p-2">
            <input type="text" v-model="bento.availability" class="w-full px-2 py-1 border rounded" />
          </td>
          <td class="border-b p-2">{{ bento.store_name || '-' }}</td>

          <!-- Actions Menu -->
          <td class="border-b p-2">
            <Menu as="div" class="relative inline-block text-left">
              <MenuButton class="w-10 h-10 bg-black bg-opacity-0 hover:bg-opacity-5 focus:bg-opacity-5">
                <DotsVerticalIcon class="h-5 w-5 text-indigo-500" />
              </MenuButton>
              <MenuItems class="absolute z-10 right-0 mt-2 w-32 bg-white shadow-lg">
                <MenuItem>
                  <router-link :to="{ name: 'app.bentos.edit', params: { id: bento.id } }" class="flex items-center px-2 py-2">
                    <PencilIcon class="mr-2 h-5 w-5 text-indigo-400" /> Edit
                  </router-link>
                </MenuItem>
                <MenuItem>
                  <button @click="deleteBento(bento)" class="flex items-center px-2 py-2">
                    <TrashIcon class="mr-2 h-5 w-5 text-indigo-400" /> Delete
                  </button>
                </MenuItem>
              </MenuItems>
            </Menu>
          </td>
        </tr>
      </tbody>

      <!-- No Bento Data Message -->
      <tbody v-else>
        <tr>
          <td colspan="10" class="text-center py-8">There are no bentos</td>
        </tr>
      </tbody>
    </table>
    </div>

    <!-- Save Button -->
    <div class="mt-6 flex justify-end">
      <button @click="confirmSave" class="bg-indigo-500 text-white py-2 px-4 rounded-md shadow hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        Save
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useStore } from "vuex";
import axiosClient from '../../axios'; // Adjust the path if necessary
import Spinner from "../../components/core/Spinner.vue";
import TableHeaderCell from "../../components/core/Table/TableHeaderCell.vue";
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import { DotsVerticalIcon, PencilIcon, TrashIcon } from "@heroicons/vue/outline";
import StoreSelect from "../Stores/StoreSelect.vue"; // Import the new StoreSelect.vue

const store = useStore();

// Reactive properties for search, pagination, sorting
const perPage = ref(10);
const search = ref('');
const sortField = ref('created_at');
const sortDirection = ref('desc');
const visitTime = ref("");  // Store visit time

// Computed state for bentos data from Vuex store
const bentos = computed(() => store.state.bentos);
const selectedStore = ref(null);  // Store the selected store ID

// On component mount, fetch the bentos data
onMounted(() => {
  fetchBentos();
});

// Function to fetch bentos data
function fetchBentos(url = null) {
  store.dispatch("getBentos", {
    url,
    search: search.value,
    per_page: perPage.value,
    sort_field: sortField.value,
    sort_direction: sortDirection.value
  });
}

// Function to handle pagination
function getForPage(event, link) {
  event.preventDefault();
  if (!link.url || link.active) return;
  fetchBentos(link.url);
}

// Function to handle sorting bentos
function sortBentos(field) {
  if (field === sortField.value) {
    sortDirection.value = sortDirection.value === 'desc' ? 'asc' : 'desc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
  fetchBentos();
}

// Function to delete a bento
function deleteBento(bento) {
  if (confirm(`Are you sure you want to delete the bento?`)) {
    store.dispatch("deleteBento", bento.id).then(() => {
      fetchBentos();
    });
  }
}

// Function to get the image URL
function getPhotoUrl(imageUrl) {
  // Ensure that the image URL is properly prefixed with `/storage/`
  if (!imageUrl.startsWith('/storage/')) {
    return `${import.meta.env.VITE_API_BASE_URL}/storage/${imageUrl}`;
  }
  return `${import.meta.env.VITE_API_BASE_URL}${imageUrl}`;
}

// Function to handle currency formatting
function formatCurrency(value) {
  if (typeof value !== 'number') return value;
  return new Intl.NumberFormat('ja-JP', {
    style: 'currency',
    currency: 'JPY',
  }).format(value);
}

// Function to fetch bentos for the selected store
function fetchBentosForStore(storeId) {
  console.log('Selected Store ID:', storeId); // Debugging line
    selectedStore.value = storeId;  // Save the selected store ID
  store.dispatch('getBentos', { storeId }).then(() => {
    console.log('Fetched bentos for store:', storeId);
  });
}

// Function to confirm and save bento updates
function confirmSave() {
  if (!visitTime.value) {
    alert('Please select a visit date/time before saving.');
    return;
  }

  if (confirm('Are you sure you want to save the updates for these bentos?')) {
    saveBentoUpdates();
  }
}

// Function to save all bento updates with visit time and store_id
function saveBentoUpdates() {
  if (!selectedStore.value) {
    alert('Please select a store before saving.');
    return;  // Exit if no store is selected
  }

  if (!confirm('Are you sure you want to save the bento updates?')) {
    return; // Exit if the admin cancels
  }

  // Iterate over each bento and add the visit_time to the dynamic fields
  const updatedBentos = bentos.value.data.map(bento => ({
    ...bento,
    visit_time: visitTime.value,  // Add visit time to each bento update
  }));

  // Send each updated bento to the backend via Axios
  updatedBentos.forEach(async (bento) => {
    try {
      // First, update the bento_store table with the current store-specific details
      await axiosClient.put(`/bentos/${bento.id}/update-dynamic`, { 
        store_id: selectedStore.value,  // Use the selected store ID here
        discounted_price: bento.usual_discounted_price,  // Update the discounted price
        discount_percentage: bento.discount_percentage,  // Update the discount percentage
        availability: bento.availability,  // Update the availability status
        visit_time: bento.visit_time  // Update the visit time
      });

      // Next, log this update into the bento_updates table
      await axiosClient.post(`/bento-updates`, {
        bento_id: bento.id,
        store_id: selectedStore.value,  // Use the selected store ID here as well
        discounted_price: bento.usual_discounted_price,
        discount_percentage: bento.discount_percentage,
        availability: bento.availability,
        visit_time: bento.visit_time
      });

      console.log(`Bento ID ${bento.id} updated successfully for store ${selectedStore.value}.`);
    } catch (error) {
      console.error(`Failed to update bento ID ${bento.id}:`, error);
    }
  });

  // Show a success message after all updates are completed
  alert('All bento updates have been saved successfully!');
}





</script>

<style scoped>
/* Mobile-first approach */
.table-auto {
  width: 100%;
  display: block;
  overflow-x: auto; /* Enable horizontal scroll on mobile */
  white-space: nowrap; /* Prevent text wrapping in table cells */
}

/* Add padding and margin for smaller devices */
.p-4 {
  padding: 1rem;
}

/* Flexbox adjustments for mobile */
.flex {
  display: flex;
  flex-direction: column; /* Stack elements vertically on small screens */
}

/* Responsive controls */
@media (min-width: 600px) {
  /* For tablets and larger */
  .flex {
    flex-direction: row; /* Stack elements horizontally on larger screens */
    justify-content: space-between;
  }
}

/* Table cells on smaller screens */
@media (max-width: 600px) {
  /* Adjust table cell padding */
  td, th {
    padding: 0.5rem;
  }
  
  /* Hide columns that are less important on mobile */
  th:nth-child(3), td:nth-child(3), /* Example of hiding a column */
  th:nth-child(4), td:nth-child(4) {
    display: none;
  }
}

/* Button styling for mobile */
button {
  width: 100%;
  padding: 12px 20px;
  margin-top: 10px;
}

/* Improve readability of font sizes */
body {
  font-size: 16px;
}

/* Responsive pagination links */
nav a {
  display: inline-block;
  padding: 8px 12px;
  font-size: 14px;
}

.table-wrapper {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch; /* Enable smooth scrolling */
}

table {
  width: 100%;
  border-collapse: collapse;
}

</style>
