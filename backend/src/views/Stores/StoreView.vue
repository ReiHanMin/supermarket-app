<template>
  <div v-if="store">

    <!--  Store Details-->
    <div>
      <h2 class="flex justify-between items-center text-xl font-semibold pb-2 border-b border-gray-300">
        Store Details
      </h2>
      <table>
        <tbody>
        <tr>
          <td class="font-bold py-1 px-2">Store Name</td>
          <td>{{ store.name }}</td>
        </tr>
        <tr>
          <td class="font-bold py-1 px-2">Chain Name</td>
          <td>{{ store.chain_name }}</td>
        </tr>
        <tr>
          <td class="font-bold py-1 px-2">Store ID</td>
          <td>{{ store.id }}</td>
        </tr>
        <tr>
          <td class="font-bold py-1 px-2">Date Added</td>
          <td>{{ store.created_at }}</td>
        </tr>
        </tbody>
      </table>
    </div>
    <!--/  Store Details-->

    <!--  Store Contact Details-->
    <div>
      <h2 class="text-xl font-semibold mt-6 pb-2 border-b border-gray-300">Store Contact Details</h2>
      <table>
        <tbody>
        <tr>
          <td class="font-bold py-1 px-2">Email</td>
          <td>{{ store.email }}</td>
        </tr>
        <tr>
          <td class="font-bold py-1 px-2">Phone</td>
          <td>{{ store.phone }}</td>
        </tr>
        <tr>
          <td class="font-bold py-1 px-2">Location</td>
          <td>{{ store.location }}</td>
        </tr>
        </tbody>
      </table>
    </div>
    <!--/  Store Contact Details-->

    <!--  Store Address-->
    <div>
      <h2 class="text-xl font-semibold mt-6 pb-2 border-b border-gray-300">Store Address</h2>
      <!--  Store Address Details-->
      <div>
        {{ store.address }} <br>
      </div>
      <!--/  Store Address Details-->
    </div>
    <!--/  Store Address-->

  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import store from "../../store";
import { useRoute } from "vue-router";
import axiosClient from "../../axios.js";

const route = useRoute();

const storeData = ref(null);

onMounted(() => {
  // Fetch the store details based on store ID
  store.dispatch('getStores', route.params.id)
    .then(({ data }) => {
      storeData.value = data;
    });
});
</script>

<style scoped>

</style>
