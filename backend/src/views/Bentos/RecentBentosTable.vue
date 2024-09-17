<template>
  <div class="bg-white p-4 rounded-lg shadow animate-fade-in-down">
    <div class="flex justify-between border-b-2 pb-3">
      <div class="flex items-center">
        <span class="whitespace-nowrap mr-3">Per Page</span>
        <select @change="getBentos(null)" v-model="perPage"
                class="appearance-none relative block w-24 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
        <span class="ml-3">Found {{ bentos.total }} bentos</span>
      </div>
      <div>
        <input v-model="search" @change="getBentos(null)"
               class="appearance-none relative block w-48 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
               placeholder="Type to Search bentos">
      </div>
    </div>

    <table class="table-auto w-full">
      <thead>
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Store Name</th>
        <th>Discount Percentage</th>
        <th>Added On</th>
        <th>Actions</th>
      </tr>
      </thead>
      <tbody v-if="bentos.loading || !bentos.data.length">
      <tr>
        <td colspan="6">
          <Spinner v-if="bentos.loading" />
          <p v-else class="text-center py-8 text-gray-700">
            There are no bentos
          </p>
        </td>
      </tr>
      </tbody>
      <tbody v-else>
      <tr v-for="(bento, index) of bentos.data" :key="bento.id">
        <td class="border-b p-2">{{ bento.id }}</td>
        <td class="border-b p-2">{{ bento.name }}</td>
        <td class="border-b p-2">{{ bento.store_name }}</td>
        <td class="border-b p-2">{{ bento.discount_percentage }}%</td>
        <td class="border-b p-2">{{ bento.added_on }}</td>
        <td class="border-b p-2">
          <router-link :to="{name: 'app.bentos.view', params: {id: bento.id}}" class="text-indigo-700 font-semibold">
            View
          </router-link>
        </td>
      </tr>
      </tbody>
    </table>

    <div v-if="!bentos.loading" class="flex justify-between items-center mt-5">
      <div v-if="bentos.data.length">
        Showing from {{ bentos.from }} to {{ bentos.to }}
      </div>
      <nav v-if="bentos.total > bentos.limit" class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        <a v-for="(link, i) of bentos.links" :key="i" :disabled="!link.url" href="#" @click="getForPage($event, link)" aria-current="page"
           class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap"
           :class="[
              link.active ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
              i === 0 ? 'rounded-l-md' : '',
              i === bentos.links.length - 1 ? 'rounded-r-md' : '',
              !link.url ? ' bg-gray-100 text-gray-700': ''
            ]"
           v-html="link.label"
        ></a>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import store from "../../store";
import Spinner from "../../components/core/Spinner.vue";

const perPage = ref(20);
const search = ref('');
const bentos = computed(() => store.state.bentos);

onMounted(() => {
  getBentos();
});

function getForPage(ev, link) {
  ev.preventDefault();
  if (!link.url || link.active) {
    return;
  }

  getBentos(link.url);
}

function getBentos(url = null) {
  store.dispatch("getBentos", {
    url,
    search: search.value,
    per_page: perPage.value,
  });
}
</script>

<style scoped>
/* Add any scoped styles here if needed */
</style>
