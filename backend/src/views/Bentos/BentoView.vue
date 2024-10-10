<template>
  <div class="p-6 bg-white rounded-lg shadow animate-fade-in-down">
    <h1 class="text-2xl font-semibold mb-4">Bento Details</h1>

    <!-- Display a loading spinner while data is being fetched -->
    <Spinner v-if="loading" />

    <!-- Display the Bento details when data is loaded -->
    <div v-else>
      <div class="mb-4">
       <img
            v-if="bento.image_url"
            class="w-16 h-16 object-cover"
            :src="getPhotoUrl(bento.image_url)"
            :alt="bento.name"
          />
        <h2 class="text-xl font-bold">{{ bento.name }}</h2>
        <p class="text-gray-600">Price: ¥{{ bento.original_price }}</p>
        <p class="text-gray-600">Discounted Price: ¥{{ bento.usual_discounted_price }}</p> 
        <p class="text-gray-600">Store: {{ bento.store_name }}</p>
        <p class="text-gray-600">Discount: {{ bento.discount_percentage }}%</p>
        <p class="text-gray-600">Description: {{ bento.description }}</p>
        <p class="text-gray-600">Calories: {{ bento.calories }} kcal</p>
        <p class="text-gray-600">Availability: {{ bento.availability }}</p>

        <!-- Display total counts for likes, dislikes, and comments -->
        <p class="text-gray-600">Likes: {{ likeCount }}</p>
        <p class="text-gray-600">Dislikes: {{ dislikeCount }}</p>
        <p class="text-gray-600">Comments: {{ commentCount }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import Spinner from '../../components/core/Spinner.vue';
import axiosClient from '../../axios.js';

const route = useRoute();
const loading = ref(true);
const bento = ref({});
const likeCount = ref(0);
const dislikeCount = ref(0);
const commentCount = ref(0); // New state for comment count

onMounted(() => {
  const id = route.params.id;
  if (id) {
    fetchBentoDetails(id);
  }
});

function fetchBentoDetails(id) {
  axiosClient.get(`/bentos/${id}`)
  .then(response => {
    console.log(response.data); // Check if the price fields are included
    bento.value = response.data;
    fetchStoreDetails(bento.value.store_id);
    loading.value = false;
  })
  .catch(error => {
    console.error('Error fetching bento details:', error);
    loading.value = false;
  });

}

function fetchStoreDetails(storeId) {
  axiosClient.get(`/stores/${storeId}`)
    .then(response => {
      bento.value.store_name = response.data.name; // Assuming the store's name is returned
    })
    .catch(error => {
      console.error('Error fetching store details:', error);
    });
}

function getPhotoUrl(imageUrl) {
  return `${import.meta.env.VITE_API_BASE_URL}/storage/${imageUrl}`; // For Vite
}

</script>

<style scoped>
/* Add any custom styles for this view */
</style>
