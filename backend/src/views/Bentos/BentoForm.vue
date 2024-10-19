<template>
  <div>
    <h2>{{ formMode }} Bento</h2>

    <!-- Store Selection -->
    <label for="store">Select Store</label>
    <treeselect
      v-model="selectedStore"
      :multiple="false"
      :options="storeOptions"
      :clearable="true"
      placeholder="Search or select a store"
      :required="true"
    />


    <!-- Display batch bento input area once store is selected -->
    <div v-if="selectedStore">
      <h2>Batch Input for Bentos at {{ selectedStore.label }}</h2>

      <div v-for="(bento, index) in bentos" :key="index" class="bento-entry">

        <!-- Upload Image -->
        <div class="file-upload">
          <label for="image_url" class="file-label">Upload Image</label>
          
          <!-- Display existing or newly uploaded image if available -->
          <div v-if="bento.image_url">
            <img :src="bento.image_url" alt="Current Image" class="uploaded-image-preview" />
            <!-- Button to delete current image -->
            <button type="button" class="btn btn-danger delete-image-button" @click="deleteCurrentImage(index)">
              Delete Current Image
            </button>
          </div>
          
          <!-- Show file input only if there is no image or the current image has been deleted -->
          <input v-if="!bento.image_url" type="file" class="file-input" @change="(event) => handleImageUpload(event, index)" />
        </div>

        <!-- Bento Name and Prices -->
        <CustomInput v-model="bento.name" label="Bento Name" :error="errors[index]?.name" />
        <CustomInput v-model="bento.original_price" label="Original Price" type="number" prepend="¥" :error="errors[index]?.original_price" />
        <CustomInput v-model="bento.usual_discounted_price" label="Discounted Price" type="number" prepend="¥" :error="errors[index]?.usual_discounted_price" />
        <CustomInput v-model="bento.discount_percentage" label="Discount Percentage" type="number" prepend="%" :disabled="true" />

        
        <!-- Availability Input (Numeric) -->
        <div class="form-group">
          <label for="availability">Availability (Number of Bentos Available)</label>
          <input 
            type="number" 
            v-model="bento.availability" 
            class="form-control" 
            min="0" 
            placeholder="Enter number of available bentos"
          />
          <span v-if="errors[index]?.availability" class="text-danger">{{ errors[index]?.availability }}</span>
        </div>


        <!-- Additional Info -->
        <CustomInput v-model="bento.calories" label="Calories" type="number" :error="errors[index]?.calories" />
        <CustomInput v-model="bento.description" label="Description" :error="errors[index]?.description" />
      </div>

      <!-- Visit Time Field -->
      <div class="form-group mt-4">
        <label for="visit-time">Visit Date & Time</label>
        <input
          id="visit-time"
          type="datetime-local"
          v-model="visitTime"
          class="form-control"
        />
      </div>

      <!-- Add Another Bento Button -->
      <button @click="addBento" class="btn btn-secondary mt-4">Add Another Bento</button>

      <!-- Save Button -->
      <button @click="confirmSave" :disabled="isSubmitting || !selectedStore" class="btn btn-primary mt-4">
        Save Bentos
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axiosClient from '../../axios.js';
import Treeselect from 'vue3-treeselect';
import 'vue3-treeselect/dist/vue3-treeselect.css';
import CustomInput from '../../components/core/CustomInput.vue';
import { useRoute, useRouter } from 'vue-router';

const selectedStore = ref(null);
const storeOptions = ref([]);
const bentos = ref([
  { 
    name: '', 
    original_price: '', 
    usual_discounted_price: '', 
    discount_percentage: '', 
    calories: '', 
    description: '', 
    availability: 0  // Set default availability
  }
]);
const errors = ref([]);
const isSubmitting = ref(false);
const uploadedImages = ref([]); // Used to store uploaded images
const formMode = ref('Create'); // Default mode is create
const visitTime = ref(null); // Store visit time
const route = useRoute(); // Get the current route
const router = useRouter(); // Define the router hook

// If editing, fetch the bento data to prefill the form
onMounted(() => { 
  if (route.params.id) {
    formMode.value = 'Edit';
    fetchStores(); // Ensure that stores are fetched first before fetching the bento
  } else {
    fetchStores();
  }
});


// Function to initialize the CSRF cookie
async function initializeCsrf() {
  try {
    await axiosClient.get('/sanctum/csrf-cookie');
    console.log('CSRF token initialized.');
  } catch (error) {
    console.error('Error initializing CSRF token:', error);
  }
}

// Handle image upload for each bento
function handleImageUpload(event, index) {
  const file = event.target.files[0];
  if (file) {
    uploadedImages.value[index] = file; // Store the uploaded file at the correct index

    // Create an object URL for preview
    bentos.value[index].image_url = URL.createObjectURL(file); // Temporary preview URL
    bentos.value[index].deleted_image = false; // Ensure we know the image is not deleted
  }
}

// Handle deleting the current image
function deleteCurrentImage(index) {
  bentos.value[index].image_url = null;  // Remove the image URL from the bento (either preloaded or newly uploaded)
  bentos.value[index].deleted_image = true;  // Set a flag that the image has been deleted
  uploadedImages.value[index] = null;  // Ensure no new image is uploaded

  // If a temporary preview URL was created, revoke it
  if (bentos.value[index].temp_url) {
    URL.revokeObjectURL(bentos.value[index].temp_url);
    bentos.value[index].temp_url = null;
  }
}

// Fetch stores for the dropdown
function fetchStores() {
  axiosClient.get('/stores')
    .then(response => {
      const storesData = response.data.data; // Adjust for pagination or structure
      if (Array.isArray(storesData)) {
        storeOptions.value = storesData.map(store => ({
          id: store.id,
          label: `${store.name} - ${store.address}`
        }));
      } else {
        console.error('Unexpected response format:', response.data);
      }

      if (formMode.value === 'Edit') {
        fetchBento(route.params.id);  // Now fetch the bento data after the stores are loaded
      }
    })
    .catch(error => {
      console.error('Error fetching stores:', error);
    });
}

function fetchBento(id) {
  axiosClient.get(`/bentos/${id}`)
    .then(response => {
      const bentoData = response.data;

      // Prepopulate the form with the fetched bento data
      bentos.value = [bentoData];

      // Ensure the image URL has the correct base URL
      if (bentoData.image_url && !bentoData.image_url.startsWith('http')) {
        const baseUrl = `${import.meta.env.VITE_API_BASE_URL}`;  // Ensure the correct base URL
        bentoData.image_url = `${baseUrl}${bentoData.image_url}`;  // Append base URL if needed
      }

      // Check if the response has store_id and set the selected store
      if (bentoData.store_id) {
        const selectedStoreOption = storeOptions.value.find(option => option.id === bentoData.store_id);
        if (selectedStoreOption) {
          selectedStore.value = selectedStoreOption;  // Prepopulate the store object correctly
        }
      }

      console.log('Bento data:', bentoData);
      console.log('Selected Store:', selectedStore.value);
    })
    .catch(error => {
      console.error('Error fetching bento:', error);
    });
}



// Function to check if a bento already exists





async function confirmSave() {
  const storeId = selectedStore.value;
  console.log("Selected Store ID:", storeId);  // Debugging line

  for (let bento of bentos.value) {
    if (bento.id) {
      // This is an edit, no need to check for duplicates, proceed to save
      await saveBentos(bento.id);  // Pass the existing bento ID to `saveBentos`
      return;
    }

    // Call the backend to check if the bento exists (only for new bentos)
    const response = await axiosClient.get('/bentos/check', {
      params: { name: bento.name, store_id: storeId },
      withCredentials: true,
    });

    const existsInSameStore = response.data.exists_in_same_store;
    const existsInOtherStore = response.data.exists_in_other_store;

    if (existsInSameStore) {
      alert(`The bento "${bento.name}" already exists in the selected store.`);
      return;  // Stop if a duplicate is found
    } else if (existsInOtherStore) {
      if (confirm(`The bento "${bento.name}" already exists in another store. Do you want to link it to the selected store?`)) {
        await axiosClient.post('/bentos/link-store', {
          bento_id: response.data.bento_id,
          store_id: storeId,
          discounted_price: bento.discounted_price,
          availability: bento.availability,
          discount_percentage: bento.discount_percentage
        });
      }
    } else {
      // Call the saveBentos method to create a new bento if no duplicates were found
      await saveBentos();
    }
  }
}





async function saveBentos(bentoId = null) {
  isSubmitting.value = true;
  const formData = new FormData();
  const token = localStorage.getItem('TOKEN');  // Retrieve the token from localStorage

  // Append form data for each bento in the list
  bentos.value.forEach((bento, index) => {
    formData.append(`bentos[${index}][name]`, bento.name);
    formData.append(`bentos[${index}][original_price]`, bento.original_price);
    formData.append(`bentos[${index}][usual_discounted_price]`, bento.usual_discounted_price);
    formData.append(`bentos[${index}][discount_percentage]`, bento.discount_percentage);
    formData.append(`bentos[${index}][calories]`, bento.calories || '');
    formData.append(`bentos[${index}][description]`, bento.description || '');
    formData.append(`bentos[${index}][availability]`, bento.availability);

    const storeId = typeof selectedStore.value === 'object' ? selectedStore.value.id : selectedStore.value;
    formData.append(`bentos[${index}][store_id]`, storeId);

    // Append image if available
    if (uploadedImages.value[index]) {
      formData.append(`bentos[${index}][image_url]`, uploadedImages.value[index]);
    }
  });

  // Debugging: Log formData content to console before submission
  console.log('FormData content before submission:');
  for (let pair of formData.entries()) {
    console.log(`${pair[0]}: ${pair[1]}`);
  }

  // Headers for Authorization and Content-Type
  const headers = {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'multipart/form-data'
  };

  // If formMode is 'Edit', update the existing bento (PUT request)
  if (formMode.value === 'Edit') {
    formData.append('_method', 'PUT');  // Laravel handles this as a PUT request

    // Use axios to send a POST request with _method=PUT for updating
    axiosClient.post(`/bentos/${route.params.id}`, formData, { headers })
      .then(() => {
        alert('Bento updated successfully');
        resetForm();  // Reset the form

        // Redirect to the bentos page
        router.push({ name: 'app.bentos' });  // Ensure you use the correct route name
      })
      .catch(error => {
        if (error.response && error.response.status === 422) {
          // Log validation errors if present
          console.error('Validation error:', error.response.data.errors);
          alert('Validation failed: ' + JSON.stringify(error.response.data.errors));
        } else {
          console.error('Error updating bento:', error);
        }
      })
      .finally(() => {
        isSubmitting.value = false;  // Ensure isSubmitting is set back to false
      });
  } else {
    // Otherwise, it's a new bento creation (POST request)
    axiosClient.post('/bentos/batch', formData, { headers })
      .then(response => {
        alert('Bentos saved successfully');
        resetForm();  // Reset the form

        // Redirect to the bentos page
        router.push({ name: 'app.bentos' });  // Ensure you use the correct route name
      })
      .catch(err => {
        console.error('Error saving bentos:', err);
      })
      .finally(() => {
        isSubmitting.value = false;  // Ensure isSubmitting is set back to false
      });
  }
}








// Add another bento entry
function addBento() {
  bentos.value.push({ 
    name: '', 
    original_price: '', 
    usual_discounted_price: '', 
    discount_percentage: '', 
    calories: '', 
    description: '', 
    availability: 0  // Set default value for new bentos
  });
  errors.value.push({});
}

// Automatically calculate discount percentage
watch(bentos, (newBentos) => {
  newBentos.forEach(bento => {
    if (bento.original_price && bento.usual_discounted_price) {
      const originalPrice = parseFloat(bento.original_price);
      const discountedPrice = parseFloat(bento.usual_discounted_price);

      if (!isNaN(originalPrice) && !isNaN(discountedPrice)) {
        bento.discount_percentage = Math.round(((originalPrice - discountedPrice) / originalPrice) * 100);
      } else {
        bento.discount_percentage = '';
      }
    } else {
      bento.discount_percentage = '';
    }
  });
}, { deep: true });

// Reset the form
function resetForm() {
  bentos.value = [{
    name: '',
    original_price: '',
    usual_discounted_price: '',
    discount_percentage: '',
    calories: '',
    description: '',
    availability: 0
  }];
  selectedStore.value = null;
  visitTime.value = null; // Reset visit time
  errors.value = [];
  uploadedImages.value = [];
}
</script>

<style scoped>
.bento-entry {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 2rem;
}

.file-upload {
  margin-top: 10px;
}

.file-label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.file-input {
  display: block;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  width: 100%;
  cursor: pointer;
}

button {
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 20px;
}

.btn-primary {
  background-color: #007bff;
  color: white;
  font-weight: bold;
  transition: background-color 0.2s;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
  font-weight: bold;
  transition: background-color 0.2s;
}

.btn-secondary:hover {
  background-color: #5a6268;
}

.uploaded-image-preview {
  width: 150px; /* Adjust the width as needed */
  height: auto; /* Keep the aspect ratio */
  border-radius: 5px; /* Optional: add some styling */
  margin-bottom: 10px; /* Optional: add some space around the image */
}

.delete-image-button {
  background-color: #dc3545; /* Bootstrap's danger color */
  color: white;
  border: none;
  padding: 10px 20px;
  font-size: 14px;
  cursor: pointer;
  border-radius: 4px;
  margin-top: 10px;
  transition: background-color 0.3s ease;
}

.delete-image-button:hover {
  background-color: #c82333;
}
</style>
