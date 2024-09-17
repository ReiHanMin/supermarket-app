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
          <TableHeaderCell field="id" :sort-field="sortField" :sort-direction="sortDirection" @click="sortBentos('id')">ID</TableHeaderCell>
          <TableHeaderCell field="image" :sort-field="sortField" :sort-direction="sortDirection">Image</TableHeaderCell>
          <TableHeaderCell field="name" :sort-field="sortField" :sort-direction="sortDirection" @click="sortBentos('name')">Name</TableHeaderCell>
          <TableHeaderCell field="category" :sort-field="sortField" :sort-direction="sortDirection" @click="sortBentos('category')">Category</TableHeaderCell>
          <TableHeaderCell field="original_price" :sort-field="sortField" :sort-direction="sortDirection" @click="sortBentos('original_price')">Original Price (¥)</TableHeaderCell>
          <TableHeaderCell field="usual_discount_percentage" :sort-field="sortField" :sort-direction="sortDirection" @click="sortBentos('usual_discount_percentage')">Usual Discount (%)</TableHeaderCell>
          <TableHeaderCell field="usual_discounted_price" :sort-field="sortField" :sort-direction="sortDirection" @click="sortBentos('usual_discounted_price')">Discounted Price (¥)</TableHeaderCell>
          <TableHeaderCell field="discount_time" :sort-field="sortField" :sort-direction="sortDirection" @click="sortBentos('discount_time')">Est. Discount Time</TableHeaderCell>
          <TableHeaderCell field="discount_status" :sort-field="sortField" :sort-direction="sortDirection" @click="sortBentos('discount_status')">Discount Status</TableHeaderCell>
          <TableHeaderCell field="stock_message" :sort-field="sortField" :sort-direction="sortDirection">Stock Message</TableHeaderCell>
          <TableHeaderCell field="chain_name" :sort-field="sortField" :sort-direction="sortDirection" @click="sortBentos('chain_name')">
          Chain Name
          </TableHeaderCell>
          <TableHeaderCell field="store_name" :sort-field="sortField" :sort-direction="sortDirection" @click="sortBentos('store_name')">
            Store Name
          </TableHeaderCell>
          <TableHeaderCell field="related_items">Related Items</TableHeaderCell>
          <TableHeaderCell field="reviews" :sort-field="sortField" :sort-direction="sortDirection">Reviews/Rating</TableHeaderCell>
          <TableHeaderCell field="actions">Actions</TableHeaderCell>
        </tr>
      </thead>
      <tbody v-if="bentos.loading || !bentos.data || !Array.isArray(bentos.data) || !bentos.data.length">
        <tr>
          <td colspan="14">
            <Spinner v-if="bentos.loading"/>
            <p v-else class="text-center py-8 text-gray-700">There are no bentos</p>
          </td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr v-for="(bento, index) of bentos.data" :key="bento.id">
          <td class="border-b p-2">{{ bento.id }}</td>
          <td class="border-b p-2">
            <img v-if="bento.image_url" class="w-16 h-16 object-cover" :src="bento.image_url" :alt="bento.name">
            <img v-else class="w-16 h-16 object-cover" src="../../assets/noimage.png">
          </td>
          <td class="border-b p-2 max-w-[200px] whitespace-nowrap overflow-hidden text-ellipsis">{{ bento.name }}</td>
          <td class="border-b p-2">{{ bento.category }}</td>
          <td class="border-b p-2">{{ formatCurrency(bento.original_price) }}</td>
          <td class="border-b p-2">{{ bento.usual_discount_percentage ? bento.usual_discount_percentage + '%' : '-' }}</td>
          <td class="border-b p-2">{{ formatCurrency(bento.usual_discounted_price) }}</td>
          <td class="border-b p-2">{{ bento.discount_time || '-' }}</td>
          <td class="border-b p-2">{{ bento.discount_status }}</td>
          <td class="border-b p-2">{{ bento.stock_message }}</td>
          <td class="border-b p-2">{{ bento.chain_name }}</td>
          <td class="border-b p-2">{{ bento.store_name }}</td>
          <td class="border-b p-2">
            <button @click="editRelatedItems(bento)">Edit/Add Related Items</button>
          </td>
          <td class="border-b p-2">
            ⭐⭐⭐⭐☆ ({{ bento.average_rating }}) ({{ bento.total_reviews }})
            <button @click="manageReviews(bento)">Manage Reviews</button>
          </td>
          <td class="border-b p-2">
            <Menu as="div" class="relative inline-block text-left">
              <div>
                <MenuButton
                  class="inline-flex items-center justify-center w-full justify-center rounded-full w-10 h-10 bg-black bg-opacity-0 text-sm font-medium text-white hover:bg-opacity-5 focus:bg-opacity-5 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75"
                >
                  <DotsVerticalIcon class="h-5 w-5 text-indigo-500" aria-hidden="true"/>
                </MenuButton>
              </div>
              <transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
              >
                <MenuItems
                  class="absolute z-10 right-0 mt-2 w-32 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                >
                  <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                      <router-link
                        :to="{name: 'app.bentos.edit', params: {id: bento.id}}"
                        :class="[
                          active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                          'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]"
                      >
                        <PencilIcon :active="active" class="mr-2 h-5 w-5 text-indigo-400" aria-hidden="true"/>
                        Edit
                      </router-link>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                      <button
                        :class="[
                          active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                          'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]"
                        @click="deleteBento(bento)"
                      >
                        <TrashIcon :active="active" class="mr-2 h-5 w-5 text-indigo-400" aria-hidden="true"/>
                        Delete
                      </button>
                    </MenuItem>
                  </div>
                </MenuItems>
              </transition>
            </Menu>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="!bentos.loading" class="flex justify-between items-center mt-5">
      <div v-if="bentos.data.length">
        Showing from {{ bentos.from }} to {{ bentos.to }}
      </div>
      <nav
        v-if="bentos.total > bentos.limit"
        class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px"
        aria-label="Pagination"
      >
        <a
          v-for="(link, i) of bentos.links"
          :key="i"
          :disabled="!link.url"
          href="#"
          @click="getForPage($event, link)"
          aria-current="page"
          class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap"
          :class="[
              link.active
                ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
              i === 0 ? 'rounded-l-md' : '',
              i === bentos.links.length - 1 ? 'rounded-r-md' : '',
              !link.url ? ' bg-gray-100 text-gray-700': ''
            ]"
          v-html="link.label"
        >
        </a>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import store from "../../store";
import Spinner from "../../components/core/Spinner.vue";
import { BENTOS_PER_PAGE } from "../../constants";
import TableHeaderCell from "../../components/core/Table/TableHeaderCell.vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { DotsVerticalIcon, PencilIcon, TrashIcon } from '@heroicons/vue/outline';

const perPage = ref(BENTOS_PER_PAGE);
const search = ref('');
const bentos = computed(() => store.state.bentos);
const sortField = ref('updated_at');
const sortDirection = ref('desc');

const bento = ref({});

onMounted(() => {
  getBentos();
});

function formatCurrency(value) {
  if (typeof value !== "number") {
    return value;
  }
  return new Intl.NumberFormat('ja-JP', {
    style: 'currency',
    currency: 'JPY'
  }).format(value);
}

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
    sort_field: sortField.value,
    sort_direction: sortDirection.value,
  });
}

function sortBentos(field) {
  if (field === sortField.value) {
    sortDirection.value = sortDirection.value === 'desc' ? 'asc' : 'desc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }

  getBentos();
}

function deleteBento(bento) {
  if (!confirm(`Are you sure you want to delete this bento?`)) {
    return;
  }
  store.dispatch('deleteBento', bento.id)
    .then(res => {
      store.commit('showToast', 'Bento was successfully deleted');
      store.dispatch('getBentos');
    });
}

function editRelatedItems(bento) {
  // Logic to manage related items
}

function manageReviews(bento) {
  // Logic to manage reviews
}
</script>

<style scoped></style>
