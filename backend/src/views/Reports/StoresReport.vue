<template>
  <BarChart :data="chartData" :height="240"/>
</template>

<script setup>

import axiosClient from "../../axios.js";
import {ref, watch} from "vue";
import BarChart from "../../components/core/Charts/Bar.vue";
import {useRoute} from "vue-router";

const route = useRoute();
const chartData = ref([]);

// Fetch the store report data when the route changes
watch(route, () => {
  getData();
}, {immediate: true})

// Function to fetch data related to stores from the report API
function getData() {
  axiosClient.get('report/stores', {params: {d: route.params.date}})
    .then(({data}) => {
      chartData.value = data;
    })
}
</script>

<style scoped>

</style>
