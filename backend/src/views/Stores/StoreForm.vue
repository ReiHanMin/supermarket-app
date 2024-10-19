<template>
  <div class="flex items-center justify-between mb-3">
    <h1 v-if="!loading" class="text-3xl font-semibold">
      {{ storeData.id ? `Edit Store: "${storeData.name}"` : 'Create New Store' }}
    </h1>
  </div>
  <div class="bg-white rounded-lg shadow animate-fade-in-down">
    <Spinner v-if="loading" class="absolute left-0 top-0 bg-white right-0 bottom-0 flex items-center justify-center z-50" />
    <form v-if="!loading" @submit.prevent="onSubmit">
      <div class="grid grid-cols-3">
        <div class="col-span-2 px-4 pt-5 pb-4">
          <!-- Store Photo Input -->
          <div class="mb-4">
            <label class="font-semibold">Store Photo</label>

            <!-- Display existing store photo if available -->
            <div v-if="storeData.photo && !photoPreview">
              <img :src="getPhotoUrl(storeData.photo)" alt="Current Store Photo" class="uploaded-image-preview mb-2 h-24 w-24 object-cover rounded-md" />
              <!-- Button to delete the current store photo -->
              <button
                type="button"
                class="btn btn-danger delete-image-button"
                @click="deleteCurrentPhoto"
              >
                Delete Current Photo
              </button>
            </div>

            <!-- File input for uploading a new photo -->
            <CustomInput
              class="mb-2"
              @change="handlePhotoUpload"
              label="Upload New Store Photo"
              type="file"
              :errors="errors['photo']"
            />

            <!-- Preview the newly uploaded image before saving -->
            <div v-if="photoPreview">
              <img :src="photoPreview" alt="Preview New Image" class="uploaded-image-preview" />
            </div>
          </div>

          <!-- Store Name Input -->
          <CustomInput
            class="mb-2"
            v-model="storeData.name"
            label="Store Name"
            :errors="errors['name']"
            required
          />
          <!-- Chain Name Select -->
          <CustomSelect
            v-model="storeData.chain_name"
            :options="chainOptions"
            label="Chain Name"
            :errors="errors['chain_name']"
            required
          />
          <!-- Address Input -->
          <CustomInput
            class="mb-2"
            v-model="storeData.address"
            label="Address"
            :errors="errors['address']"
          />
          <!-- Latitude Input -->
          <CustomInput
            class="mb-2"
            v-model="storeData.latitude"
            label="Latitude"
            :errors="errors['latitude']"
          />
          <!-- Longitude Input -->
          <CustomInput
            class="mb-2"
            v-model="storeData.longitude"
            label="Longitude"
            :errors="errors['longitude']"
          />
          <!-- Email Input -->
          <CustomInput
            class="mb-2"
            v-model="storeData.email"
            label="Email"
            :errors="errors['email']"
          />
          <!-- Phone Input -->
          <CustomInput
            class="mb-2"
            v-model="storeData.phone"
            label="Phone"
            :errors="errors['phone']"
          />
          <!-- Weekly Opening Hours -->
          <div class="mb-4">
            <h3 class="font-semibold text-lg">Opening Hours</h3>
            <!-- Loop through each day for input -->
            <div v-for="(time, day) in weekDays" :key="day" class="mb-2">
              <label class="font-semibold">{{ day }}</label>
              <div class="flex space-x-4">
                <!-- Opening Time -->
                <input
                  class="mb-2"
                  v-model="storeData.opening_hours[day].start"
                  type="time"
                  :step="60"
                  required
                />
                <!-- Closing Time -->
                <input
                  class="mb-2"
                  v-model="storeData.opening_hours[day].end"
                  type="time"
                  :step="60"
                  required
                />
              </div>
            </div>

            <!-- Button to copy Monday's times to all other days -->
            <button 
              type="button"
              class="btn btn-primary mt-3"
              @click="copyMondayTimes"
            >
              Copy Monday Times to All Days
            </button>
          </div>
        </div>
      </div>
      <footer class="bg-gray-50 rounded-b-lg px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <button type="submit" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500">
          Save
        </button>
        <button type="button" @click="onSubmit($event, true)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500">
          Save & Close
        </button>
        <router-link :to="{ name: 'app.stores' }" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" ref="cancelButtonRef">
          Cancel
        </router-link>
      </footer>
    </form>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import CustomInput from '../../components/core/CustomInput.vue';
import CustomSelect from '../../components/core/CustomSelect.vue';
import Spinner from '../../components/core/Spinner.vue';
import { useRoute, useRouter } from 'vue-router';
import store from '../../store';

const props = defineProps({
  id: {
    type: [String, Number],
    required: false,
  },
});

const route = useRoute();
const router = useRouter();
const photoPreview = ref(null);  // Initialize the photo preview

// Define weekDays for rendering the days of the week
const weekDays = ref({
  Monday: 'Monday',
  Tuesday: 'Tuesday',
  Wednesday: 'Wednesday',
  Thursday: 'Thursday',
  Friday: 'Friday',
  Saturday: 'Saturday',
  Sunday: 'Sunday',
});

const storeData = ref({
  id: null,
  name: '',
  chain_name: '',
  address: '',
  latitude: '',
  longitude: '',
  email: '',
  phone: '',
  photo: null,
  opening_hours: {
    Monday: { start: '', end: '' },
    Tuesday: { start: '', end: '' },
    Wednesday: { start: '', end: '' },
    Thursday: { start: '', end: '' },
    Friday: { start: '', end: '' },
    Saturday: { start: '', end: '' },
    Sunday: { start: '', end: '' }
  },
});

// Watch for changes to opening_hours and log the changes
watch(
  () => storeData.value.opening_hours,
  (newVal) => {
    console.log('Opening hours changed:', newVal);
  },
  { deep: true } // Use deep watch to track changes to nested properties
);

const errors = ref({});
const loading = ref(false);
const chainOptions = [
  { value: 'Fresco', label: 'Fresco' },
  { value: 'Life', label: 'Life' },
  { value: 'Aeon', label: 'Aeon' },
  { value: 'Daikokuya', label: 'Daikokuya' },
  { value: 'Co-op Kyoto', label: 'Co-op Kyoto' },
  { value: 'Other', label: 'Other' },
];

onMounted(() => {
  if (route.params.id) {
    loading.value = true;
    store.dispatch('getStore', route.params.id)
      .then((response) => {
        loading.value = false;
        storeData.value = response.data;

        // Log to confirm initialization
        console.log('Store data after fetch:', storeData.value);

        // Ensure opening_hours is always initialized
        storeData.value.opening_hours = storeData.value.opening_hours || {};
        const defaultOpeningHours = {
          Monday: { start: '', end: '' },
          Tuesday: { start: '', end: '' },
          Wednesday: { start: '', end: '' },
          Thursday: { start: '', end: '' },
          Friday: { start: '', end: '' },
          Saturday: { start: '', end: '' },
          Sunday: { start: '', end: '' },
        };

        storeData.value.opening_hours = {
          ...defaultOpeningHours,
          ...storeData.value.opening_hours,
        };

        // Log the updated opening_hours
        console.log('Opening hours after initialization:', storeData.value.opening_hours);
      })
      .catch(err => {
        loading.value = false;
        console.error('Error fetching store:', err);
      });
  }
});

function copyMondayTimes() {
  const mondayTimes = storeData.value.opening_hours.Monday;
  
  // Loop through all the days except Monday
  for (const day in storeData.value.opening_hours) {
    if (day !== 'Monday') {
      storeData.value.opening_hours[day].start = mondayTimes.start;
      storeData.value.opening_hours[day].end = mondayTimes.end;
    }
  }
}

// Helper function to build the photo URL
function getPhotoUrl(photoPath) {
  return photoPath ? `http://localhost:8000${photoPath}` : null;
}

// Handle deleting the current photo
function deleteCurrentPhoto() {
  storeData.value.photo = null;  // Remove the photo URL from the storeData
  photoPreview.value = null;  // Ensure no new photo is previewed

  // If a temporary preview URL was created, revoke it
  if (storeData.value.temp_url) {
    URL.revokeObjectURL(storeData.value.temp_url);
    storeData.value.temp_url = null;
  }
}

function handlePhotoUpload(event) {
  const file = event.target.files ? event.target.files[0] : null;
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
    storeData.value.photo = file;
    console.log("Photo uploaded:", file.name);  // Debugging log
  } else {
    console.error("No file selected");
  }
}

function onSubmit($event, close = false) {
  loading.value = true;
  errors.value = {};

  console.log('Before submission:', storeData.value.opening_hours);

  // Create a new FormData object for submission
  const formData = new FormData();

  // Append form data from storeData (only reading, not modifying)
  formData.append('name', storeData.value.name || '');
  formData.append('chain_name', storeData.value.chain_name || '');
  formData.append('address', storeData.value.address || '');
  formData.append('latitude', storeData.value.latitude || '');
  formData.append('longitude', storeData.value.longitude || '');
  formData.append('email', storeData.value.email || '');
  formData.append('phone', storeData.value.phone || '');

  // Append opening hours data for each day of the week (only reading from storeData)
  for (const day in storeData.value.opening_hours) {
    const start = storeData.value.opening_hours[day]?.start || '';
    const end = storeData.value.opening_hours[day]?.end || '';

    formData.append(`opening_hours[${day}][start]`, start);
    formData.append(`opening_hours[${day}][end]`, end);
  }

  // Handle photo upload if available (only reading, not modifying storeData)
  if (!storeData.value.photo || !(storeData.value.photo instanceof File)) {
    console.warn("No photo uploaded, proceeding without a photo.");
  } else {
    formData.append('photo', storeData.value.photo);
  }

  // Log FormData contents for debugging
  console.log("FormData contents:", [...formData.entries()]);

  // Use the Vuex action to update or create the store
  if (storeData.value.id) {
    // Updating an existing store
    formData.append('id', storeData.value.id);  // Append the ID to the formData
    store.dispatch('updateStore', formData)  // Dispatch the action with formData
      .then(response => {
        console.log("This is the response: ", response);
        loading.value = false;
        
        // Update storeData only after the successful response
        if (response.status === 200) {
          storeData.value = { ...response.data };  // Assign new data to storeData
          store.commit('showToast', 'Store was successfully updated');
          store.dispatch('getStores');
          if (close) {
            router.push({ name: 'app.stores' });
          }
        }
      })
      .catch(err => {
        loading.value = false;
        if (err.response) {
          errors.value = err.response.data.errors || {};  // Handle validation errors
          console.error("Error response:", err.response);
        } else {
          console.error("Unexpected error:", err);
        }
      });
  } else {
    // Creating a new store
    store.dispatch('createStore', formData)
      .then(response => {
        loading.value = false;

        // Update storeData only after the successful response
        if (response.status === 201) {
          storeData.value = { ...response.data };  // Assign new data to storeData
          store.commit('showToast', 'Store was successfully created');
          store.dispatch('getStores');
          if (close) {
            router.push({ name: 'app.stores' });
          } else {
            router.push({ name: 'edit-store', params: { id: response.data.id } });
          }
        }
      })
      .catch(err => {
        loading.value = false;
        if (err.response) {
          errors.value = err.response.data.errors || {};  // Handle validation errors
          console.error("Error response:", err.response);
        } else {
          console.error("Unexpected error:", err);
        }
      });
  }
  
  return {
    storeData,
    photoPreview,
    weekDays,
    handlePhotoUpload,
    deleteCurrentPhoto,
    getPhotoUrl,
    onSubmit,
    errors,
    loading,
    copyMondayTimes
  };
}
</script>

<style scoped></style>
