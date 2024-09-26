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

        <!-- Stock and Availability -->
        <CustomInput v-model="bento.stock_message" label="Stock Message" :error="errors[index]?.stock_message" />
        
        <!-- Availability Dropdown -->
        <div class="form-group">
          <label for="availability">Availability</label>
          <select v-model="bento.availability" class="form-control">
            <option value="Many">Many</option>
            <option value="A few left">A few left</option>
            <option value="Sold out">Sold out</option>
          </select>
          <span v-if="errors[index]?.availability" class="text-danger">{{ errors[index]?.availability }}</span>
        </div>

        <!-- Additional Info -->
        <CustomInput v-model="bento.calories" label="Calories" type="number" :error="errors[index]?.calories" />
        <CustomInput v-model="bento.description" label="Description" :error="errors[index]?.description" />
      </div>

      <!-- Add Another Bento Button -->
      <button @click="addBento" class="btn btn-secondary">Add Another Bento</button>

      <!-- Submit Button -->
      <button @click="saveBentos" :disabled="isSubmitting || !selectedStore" class="btn btn-primary">Save Bentos</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axiosClient from '../../axios.js';
import Treeselect from 'vue3-treeselect';
import 'vue3-treeselect/dist/vue3-treeselect.css';
import CustomInput from '../../components/core/CustomInput.vue';
import { useRoute } from 'vue-router';

const selectedStore = ref(null);
const storeOptions = ref([]);
const bentos = ref([
  { 
    name: '', 
    original_price: '', 
    usual_discounted_price: '', 
    discount_percentage: '', 
    stock_message: '', 
    calories: '', 
    description: '', 
    availability: 'Many'  // Set default availability
  }
]);
const errors = ref([]);
const isSubmitting = ref(false);
const uploadedImages = ref([]); // Used to store uploaded images
const formMode = ref('Create'); // Default mode is create
const route = useRoute(); // Get the current route

// If editing, fetch the bento data to prefill the form
onMounted(() => {
  if (route.params.id) {
    formMode.value = 'Edit';
    fetchStores(); // Ensure that stores are fetched first before fetching the bento
  } else {
    fetchStores();
  }
});

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

// Fetch bento data for editing
function fetchBento(id) {
  axiosClient.get(`/bentos/${id}`)
    .then(response => {
      const bentoData = response.data;
      bentos.value = [bentoData]; // Prepopulate the form with the fetched bento data

      // Find and set the store in storeOptions based on store_id
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


// Handle saving the bento(s)
function saveBentos() {
  isSubmitting.value = true;
  const formData = new FormData();
  const token = localStorage.getItem('TOKEN'); // Retrieve the token from localStorage

  bentos.value.forEach((bento, index) => {
    formData.append(`bentos[${index}][name]`, bento.name);
    formData.append(`bentos[${index}][original_price]`, bento.original_price);
    formData.append(`bentos[${index}][usual_discounted_price]`, bento.usual_discounted_price);
    formData.append(`bentos[${index}][discount_percentage]`, bento.discount_percentage);
    formData.append(`bentos[${index}][stock_message]`, bento.stock_message || ''); // Handle undefined stock_message
    formData.append(`bentos[${index}][calories]`, bento.calories || '');
    formData.append(`bentos[${index}][description]`, bento.description || ''); 
    formData.append(`bentos[${index}][availability]`, bento.availability);
    
    // Append the store id properly, ensure selectedStore is an object
    const storeId = typeof selectedStore.value === 'object' ? selectedStore.value.id : selectedStore.value;
    formData.append(`bentos[${index}][store_id]`, storeId);

    // Append the image file if available
    if (uploadedImages.value[index]) {
      formData.append(`bentos[${index}][image_url]`, uploadedImages.value[index]);
    }
  });

  // Log formData to verify the data before sending
  console.log('FormData content before submission:');
  for (let pair of formData.entries()) {
    console.log(`${pair[0]}: ${pair[1]}`);
  }

  // Headers with Authorization token
  const headers = {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'multipart/form-data'
  };

  // Update mode: send PUT request
  if (formMode.value === 'Edit') {
    formData.append('_method', 'PUT');

    axiosClient.post(`/bentos/${route.params.id}`, formData, { headers })
      .then(() => {
        alert('Bento updated successfully');
      })
      .catch(error => {
        if (error.response && error.response.status === 422) {
          console.error('Validation error:', error.response.data.errors);  // Log validation errors
          alert('Validation failed: ' + JSON.stringify(error.response.data.errors));
        } else {
          console.error('Error updating bento:', error);
        }
      })
      .finally(() => {
        isSubmitting.value = false;
      });

  } else {
    // Create mode: send POST request
    axiosClient.post('/bentos/batch', formData, { headers })
      .then(response => {
        alert('Bentos saved successfully');
        resetForm();
      })
      .catch(err => {
        console.error('Error saving bentos', err);
      })
      .finally(() => {
        isSubmitting.value = false;
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
    stock_message: '', 
    calories: '', 
    description: '', 
    availability: 'Many'  // Set default value for new bentos
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
    stock_message: '',
    calories: '',
    description: '',
    availability: 'Many'
  }];
  selectedStore.value = null;
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
