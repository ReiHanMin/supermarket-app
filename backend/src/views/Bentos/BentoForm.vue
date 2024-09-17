<template>
  <div class="flex items-center justify-between mb-3">
    <h1 v-if="!loading" class="text-3xl font-semibold">
      {{ bento.id ? `Update Bento: "${bento.name}"` : 'Create New Bento' }}
    </h1>
  </div>
  <div class="bg-white rounded-lg shadow animate-fade-in-down">
    <Spinner v-if="loading" class="absolute left-0 top-0 bg-white right-0 bottom-0 flex items-center justify-center z-50" />
    <form v-if="!loading" @submit.prevent="onSubmit">
      <div class="grid grid-cols-3">
        <div class="col-span-2 px-4 pt-5 pb-4">
          <CustomInput class="mb-2" v-model="bento.name" label="Bento Name" :errors="errors['name']" />
          <CustomInput type="richtext" class="mb-2" v-model="bento.description" label="Description" :errors="errors['description']" />
          <CustomInput type="number" class="mb-2" v-model="bento.price" label="Price" prepend="¥" :errors="errors['price']" />
          <CustomInput type="number" class="mb-2" v-model="bento.quantity" label="Quantity" :errors="errors['quantity']" />
          <CustomInput type="checkbox" class="mb-2" v-model="bento.published" label="Published" :errors="errors['published']" />
          <treeselect v-model="bento.categories" :multiple="true" :options="options" :errors="errors['categories']" />
        </div>
        <div class="col-span-1 px-4 pt-5 pb-4">
          <image-preview v-model="bento.images" :images="bento.images" v-model:deleted-images="bento.deleted_images" v-model:image-positions="bento.image_positions" />
        </div>
      </div>
      <footer class="bg-gray-50 rounded-b-lg px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <button type="submit" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500">
          Save
        </button>
        <button type="button" @click="onSubmit($event, true)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500">
          Save & Close
        </button>
        <router-link :to="{ name: 'app.bentos' }" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" ref="cancelButtonRef">
          Cancel
        </router-link>
      </footer>
    </form>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import CustomInput from '../../components/core/CustomInput.vue';
import Spinner from '../../components/core/Spinner.vue';
import { useRoute, useRouter } from 'vue-router';
import ImagePreview from '../../components/ImagePreview.vue';
import Treeselect from 'vue3-treeselect';
import 'vue3-treeselect/dist/vue3-treeselect.css';
import axiosClient from '../../axios.js';
import store from '../../store';

const route = useRoute();
const router = useRouter();

const bento = ref({
  id: null,
  name: null,
  images: [],
  deleted_images: [],
  image_positions: {},
  description: '',
  price: null,
  quantity: null,
  published: false,
  categories: []
});

const errors = ref({});
const loading = ref(false);
const options = ref([]);

onMounted(() => {
  if (route.params.id) {
    loading.value = true;
    store.dispatch('getBento', route.params.id)
      .then((response) => {
        loading.value = false;
        bento.value = response.data;
      });
  }

  axiosClient.get('/categories/tree')
    .then(result => {
      options.value = result.data;
    });
});

function onSubmit($event, close = false) {
  loading.value = true;
  errors.value = {};
  bento.value.quantity = bento.value.quantity || null;
  if (bento.value.id) {
    store.dispatch('updateBento', bento.value)
      .then(response => {
        loading.value = false;
        if (response.status === 200) {
          bento.value = response.data;
          store.commit('showToast', 'Bento was successfully updated');
          store.dispatch('getBentos');
          if (close) {
            router.push({ name: 'app.bentos' });
          }
        }
      })
      .catch(err => {
        loading.value = false;
        errors.value = err.response.data.errors;
      });
  } else {
    store.dispatch('createBento', bento.value)
      .then(response => {
        loading.value = false;
        if (response.status === 201) {
          bento.value = response.data;
          store.commit('showToast', 'Bento was successfully created');
          store.dispatch('getBentos');
          if (close) {
            router.push({ name: 'app.bentos' });
          } else {
            bento.value = response.data;
            router.push({ name: 'app.bentos.edit', params: { id: response.data.id } });
          }
        }
      })
      .catch(err => {
        loading.value = false;
        errors.value = err.response.data.errors;
      });
  }
}
</script>

<style scoped></style>
