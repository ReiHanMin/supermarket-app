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
          <CustomInput
            class="mb-2"
            @change="handlePhotoUpload"
            label="Store Photo"
            type="file"
            :errors="errors['photo']"
            required
          />
          <CustomInput
            class="mb-2"
            v-model="storeData.name"
            label="Store Name"
            :errors="errors['name']"
            required
          />
          <CustomSelect
            v-model="storeData.chain_name"
            :options="chainOptions"
            label="Chain Name"
            :errors="errors['chain_name']"
            required
          />
          <CustomInput
            class="mb-2"
            v-model="storeData.address"
            label="Address"
            :errors="errors['address']"
          />
          <CustomInput
            class="mb-2"
            v-model="storeData.email"
            label="Email"
            :errors="errors['email']"
          />
          <CustomInput
            class="mb-2"
            v-model="storeData.phone"
            label="Phone"
            :errors="errors['phone']"
          />
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
import { onMounted, ref } from 'vue';
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

const storeData = ref({
  id: null,
  name: '',
  chain_name: '',
  address: '',
  email: '',
  phone: '',
  photo: null,
});

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
    store.dispatch('getStore', route.params.id)  // Fetch store details
      .then((response) => {
        loading.value = false;
        storeData.value = response.data;  // Populate form with store data
        storeData.value.id = response.data.id;  // Ensure the ID is set
      })
      .catch(err => {
        loading.value = false;
        console.error('Error fetching store:', err);
      });
  }
});

function handlePhotoUpload(event) {
  const file = event.target.files ? event.target.files[0] : null;
  if (file) {
    storeData.value.photo = file;
    console.log("Photo uploaded:", file.name);  // Debugging log
  } else {
    console.error("No file selected");
  }
}


function onSubmit($event, close = false) {
  loading.value = true;
  errors.value = {};

  const formData = new FormData();

  // Append the form data for all required fields
  formData.append('name', storeData.value.name || '');
  formData.append('chain_name', storeData.value.chain_name || '');
  formData.append('address', storeData.value.address || '');
  formData.append('email', storeData.value.email || '');
  formData.append('phone', storeData.value.phone || '');

  // Handle the photo if it exists and is a file (not a string)
  if (storeData.value.photo && storeData.value.photo instanceof File) {
    formData.append('photo', storeData.value.photo);
  } else {
    console.error("Photo is missing or invalid.");
  }

  // Log FormData contents for debugging
  console.log("FormData contents:");
  for (let [key, value] of formData.entries()) {
    console.log(`${key}: ${value}`);
  }

  if (storeData.value.id) {
    // Updating store
    store.dispatch('updateStore', formData)
      .then(response => {
        loading.value = false;
        if (response.status === 200) {
          storeData.value = response.data;
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
          errors.value = err.response.data.errors || {}; // Handle validation errors
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
        if (response.status === 201) {
          storeData.value = response.data;
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
          errors.value = err.response.data.errors || {}; // Handle validation errors
          console.error("Error response:", err.response);
        } else {
          console.error("Unexpected error:", err);
        }
      });
  }
}





</script>

<style scoped></style>
