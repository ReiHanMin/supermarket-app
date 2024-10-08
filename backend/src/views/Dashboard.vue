<template>
  <div class="mb-2 flex items-center justify-between">
    <h1 class="text-3xl font-semibold">Admin Dashboard</h1>
    <div class="flex items-center">
      <label class="mr-2">Change Date Period</label>
      <CustomInput type="select" v-model="chosenDate" @change="onDatePickerChange" :select-options="dateOptions" />
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
    <!-- Total Registered Users -->
    <div class="animate-fade-in-down bg-white py-6 px-5 rounded-lg shadow flex flex-col items-center justify-center">
      <label class="text-lg font-semibold block mb-2">Total Registered Users</label>
      <template v-if="!loading.usersCount">
        <span class="text-3xl font-semibold">{{ usersCount }}</span>
      </template>
      <Spinner v-else text="" class="" />
    </div>

    <!-- Total Bentos Listed -->
    <div class="animate-fade-in-down bg-white py-6 px-5 rounded-lg shadow flex flex-col items-center justify-center"
         style="animation-delay: 0.1s">
      <label class="text-lg font-semibold block mb-2">Total Bentos Listed</label>
      <template v-if="!loading.bentosCount">
        <span class="text-3xl font-semibold">{{ bentosCount }}</span>
      </template>
      <Spinner v-else text="" class="" />
    </div>

    <!-- Total Stores Listed -->
    <div class="animate-fade-in-down bg-white py-6 px-5 rounded-lg shadow flex flex-col items-center justify-center"
         style="animation-delay: 0.2s">
      <label class="text-lg font-semibold block mb-2">Total Stores Listed</label>
      <template v-if="!loading.storesCount">
        <span class="text-3xl font-semibold">{{ storesCount }}</span>
      </template>
      <Spinner v-else text="" class="" />
    </div>

    <!-- Total User Reviews -->
    <div class="animate-fade-in-down bg-white py-6 px-5 rounded-lg shadow flex flex-col items-center"
         style="animation-delay: 0.3s">
      <label class="text-lg font-semibold block mb-2">Total User Reviews</label>
      <template v-if="!loading.reviewsCount">
        <span class="text-3xl font-semibold">{{ reviewsCount }}</span>
      </template>
      <Spinner v-else text="" class="" />
    </div>
  </div>

  <div class="grid grid-rows-1 md:grid-rows-2 md:grid-flow-col grid-cols-1 md:grid-cols-3 gap-3">

    <!-- Recent Bentos Updates -->
<div class="col-span-1 md:col-span-2 row-span-1 md:row-span-2 bg-white py-6 px-5 rounded-lg shadow">
    <label class="text-lg font-semibold block mb-2">Recent Bentos Updates</label>

    <!-- Show spinner when loading -->
    <template v-if="loading.recentBentos">
        <Spinner text="Loading..." class="" />
    </template>

    <!-- Show recent bentos once loading is complete and data exists -->
    <template v-else-if="recentBentos && recentBentos.length > 0">
        <div v-for="b of recentBentos" :key="b?.id" class="py-2 px-3 hover:bg-gray-50">
            <router-link :to="{ name: 'app.bentos.view', params: { id: b?.id } }" >
                <p class="text-indigo-700 font-semibold">
                    Bento #{{ b?.id }}: {{ b?.name }} added on {{ b?.created_at }}.
                </p>
                <p class="flex justify-between">
                    <span>{{ b?.store_name || 'Unknown Store' }}</span>
                    <span>{{ b?.discount_percentage || 'No discount' }}% off</span>
                </p>
                <p v-if="b?.image_url">
                    <img :src="b?.image_url" alt="Bento Image" class="w-20 h-20 object-cover rounded"/>
                </p>
                <p>Ingredients: {{ b?.ingredients || 'N/A' }}</p>
                <p>Calories: {{ b?.calories || 'N/A' }} kcal</p>
                <p v-if="b?.availability">Available: {{ b?.availability }}</p>
                <p v-else>Not Available</p>
                <p>Rating: {{ b?.rating || 'No rating' }} ({{ b?.reviews_count || 0 }} reviews)</p>
            </router-link>
        </div>
    </template>

    <!-- Fallback if no bentos available -->
    <template v-else>
        <p>No recent bentos available.</p>
    </template>
</div>



    <!-- Recent Store Updates -->
    <div class="bg-white py-6 px-5 rounded-lg shadow flex flex-col items-center justify-center">
      <label class="text-lg font-semibold block mb-2">Recent Store Updates</label>
      <template v-if="!loading.recentStores">
        <DoughnutChart :width="140" :height="200" :data="recentStores" />
      </template>
      <Spinner v-else text="" class="" />
    </div>

    <!-- Latest User Feedback -->
   <div class="bg-white py-6 px-5 rounded-lg shadow">
  <label class="text-lg font-semibold block mb-2">Latest User Feedback</label>

  <!-- Show Spinner while loading -->
  <template v-if="loading.latestFeedback">
    <Spinner text="Loading..." />
  </template>

  <!-- Show Feedback once loading is complete -->
  <template v-else-if="latestFeedback && latestFeedback.length > 0">
    <div v-for="feedback in latestFeedback" :key="feedback.id" class="mb-4">
      <h3 class="font-semibold">{{ feedback.user_name }}</h3>
      <p>{{ feedback.comment }}</p>
      <small class="text-gray-500">Commented on {{ new Date(feedback.created_at).toLocaleDateString() }}</small>
    </div>
  </template>

  <!-- Fallback if no feedback available -->
  <template v-else>
    <p>No recent feedback available.</p>
  </template>
</div>


  </div>
</template>

<script setup>
import { UserIcon } from '@heroicons/vue/outline';
import DoughnutChart from '../components/core/Charts/Doughnut.vue';
import axiosClient from '../axios.js';
import { computed, onMounted, ref } from 'vue';
import Spinner from '../components/core/Spinner.vue';
import CustomInput from '../components/core/CustomInput.vue';
import { useStore } from 'vuex';

const store = useStore();
const dateOptions = computed(() => store.state.dateOptions);
const chosenDate = ref('all');

const loading = ref({
  usersCount: true,
  bentosCount: true,
  storesCount: true,
  reviewsCount: true,
  userFeedback: true,
  recentBentos: true,
  recentStores: true,
  latestFeedback: true,
});

const usersCount = ref(0);
const bentosCount = ref(0);
const storesCount = ref(0);
const reviewsCount = ref(0); // New variable for user reviews count
const userFeedbackCount = ref(0);
const recentBentos = ref([]);
const recentStores = ref([]);
const latestFeedback = ref([]);
const search = ref(''); // Initializing search as an empty string


function updateDashboard() {
  const d = chosenDate.value;
  loading.value = {
    usersCount: true,
    bentosCount: true,
    storesCount: true,
    reviewsCount: true,
    userFeedback: true,
    recentBentos: true,
    recentStores: true,
    latestFeedback: true,
  };

  axiosClient.get(`/dashboard/users-count`, { params: { d } }).then(({ data }) => {
    usersCount.value = data;
    loading.value.usersCount = false;
  });

  axiosClient.get(`/dashboard/bentos-count`, { params: { d } })
  .then(({ data }) => {
    bentosCount.value = data.count; // Extract the 'count' from the response object
    loading.value.bentosCount = false;
  })
  .catch((error) => {
    console.error('Error fetching bento count:', error);
  });



  axiosClient.get(`/dashboard/stores-count`, { params: { d } }).then(({ data }) => {
    storesCount.value = data.count;
    loading.value.storesCount = false;
  });

axiosClient.get(`/dashboard/reviews-count`, { params: { d } })
  .then(({ data }) => {
    reviewsCount.value = data.count; // Extract the 'count' from the response object
    loading.value.reviewsCount = false;
  })
  .catch((error) => {
    console.error('Error fetching review count:', error);
  });

  axiosClient.get(`/dashboard/user-feedback`).then(({ data }) => {
    latestFeedback.value = data;
    loading.value.latestFeedback = false;
}).catch(error => {
    console.error("Error fetching latest feedback:", error);
    loading.value.latestFeedback = false;
});


  
  axiosClient.get(`/dashboard/recent-bentos`, {
    params: {
    per_page: 10,           // Adjust as needed, or make this dynamic
    search: search.value,    // Bound to the initialized search ref
    sort_field: 'created_at', // Dynamic sort field
    sort_direction: 'desc'   // Sort in descending order
  }
  })
  .then(({ data }) => {
  recentBentos.value = data.data;  // Handle pagination if needed (using .data here)
  loading.value.recentBentos = false;
  })
  .catch(error => {
  console.error("Error fetching recent bentos:", error);
  loading.value.recentBentos = false;
  });




  axiosClient.get(`/dashboard/recent-stores`).then(({ data }) => {
    recentStores.value = data; // Assuming recentStores is the reactive state holding the store data
    loading.value.recentStores = false;
}).catch(error => {
    console.error('Error fetching recent stores:', error);
    loading.value.recentStores = false;
});

}

function onDatePickerChange() {
  updateDashboard();
}

onMounted(() => updateDashboard());
</script>

<style scoped>
.mb-2 {
  margin-bottom: 0.5rem;
}
.flex {
  display: flex;
}
.items-center {
  align-items: center;
}
.justify-between {
  justify-content: space-between;
}
.text-3xl {
  font-size: 1.875rem;
  line-height: 2.25rem;
}
.font-semibold {
  font-weight: 600;
}
.bg-white {
  background-color: #ffffff;
}
.py-6 {
  padding-top: 1.5rem;
  padding-bottom: 1.5rem;
}
.px-5 {
  padding-left: 1.25rem;
  padding-right: 1.25rem;
}
.rounded-lg {
  border-radius: 0.5rem;
}
.shadow {
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
}
</style>
