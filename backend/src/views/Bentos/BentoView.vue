<template>
  <div class="p-6 bg-white rounded-lg shadow animate-fade-in-down">
    <h1 class="text-2xl font-semibold mb-4">Bento Details</h1>

    <!-- Display a loading spinner while data is being fetched -->
    <Spinner v-if="loading" />

    <!-- Display the Bento details when data is loaded -->
    <div v-else>
      <div class="mb-4">
        <img :src="bento.image_url" alt="Bento Image" class="w-20 h-20 object-cover rounded" />
        <h2 class="text-xl font-bold">{{ bento.name }}</h2>
        <p class="text-gray-600">Price: ¥{{ bento.price }}</p>
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
      console.log(response.data); // Check the exact response structure
      bento.value = response.data; // Adjust as necessary based on your API response structure
      loading.value = false;
    })
    .catch(error => {
      console.error('Error fetching bento details:', error);
      loading.value = false;
    });
}

</script>

<style scoped>
/* Add any custom styles for this view */
</style>
