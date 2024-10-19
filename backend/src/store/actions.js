import axiosClient from "../axios";

export function getCurrentUser({ commit }, data) {
  return axiosClient.get('/user', data)
    .then(({ data }) => {
      commit('setUser', data);
      return data;
    });
}

export function login({ commit }, data) {
  return axiosClient.post('/login', data)
    .then(({ data }) => {
      commit('setUser', data.user);
      commit('setToken', data.token);
      return data;
    });
}

export function logout({ commit }) {
  return axiosClient.post('/logout')
    .then((response) => {
      commit('setToken', null);
      return response;
    });
}

export function getCountries({ commit }) {
  return axiosClient.get('countries')
    .then(({ data }) => {
      commit('setCountries', data);
    });
}

export function getOrders({ commit, state }, { url = null, search = '', per_page, sort_field, sort_direction } = {}) {
  commit('setOrders', [true]);
  url = url || '/orders';
  const params = {
    per_page: state.orders.limit,
  };
  return axiosClient.get(url, {
    params: {
      ...params,
      search, per_page, sort_field, sort_direction,
    }
  })
    .then((response) => {
      commit('setOrders', [false, response.data]);
    })
    .catch(() => {
      commit('setOrders', [false]);
    });
}

export function getOrder({ commit }, id) {
  return axiosClient.get(`/orders/${id}`);
}

export function getProducts({ commit, state }, { url = null, search = '', per_page, sort_field, sort_direction } = {}) {
  commit('setProducts', [true]);
  url = url || '/products';
  const params = {
    per_page: state.products.limit,
  };
  return axiosClient.get(url, {
    params: {
      ...params,
      search, per_page, sort_field, sort_direction,
    }
  })
    .then((response) => {
      commit('setProducts', [false, response.data]);
    })
    .catch(() => {
      commit('setProducts', [false]);
    });
}

export function getProduct({ commit }, id) {
  return axiosClient.get(`/products/${id}`);
}

export function createProduct({ commit }, product) {
  if (product.images && product.images.length) {
    const form = new FormData();
    form.append('title', product.title);
    product.images.forEach(im => form.append('images[]', im));
    form.append('description', product.description || '');
    form.append('published', product.published ? 1 : 0);
    form.append('price', product.price);
    product = form;
  }
  return axiosClient.post('/products', product);
}

export function updateProduct({ commit }, product) {
  const id = product.id;
  if (product.images && product.images.length) {
    const form = new FormData();
    form.append('id', product.id);
    form.append('title', product.title);
    product.images.forEach(im => form.append(`images[${im.id}]`, im));
    if (product.deleted_images) {
      product.deleted_images.forEach(id => form.append('deleted_images[]', id));
    }
    for (let id in product.image_positions) {
      form.append(`image_positions[${id}]`, product.image_positions[id]);
    }
    form.append('description', product.description || '');
    form.append('published', product.published ? 1 : 0);
    form.append('price', product.price);
    form.append('_method', 'PUT');
    product = form;
  } else {
    product._method = 'PUT';
  }
  return axiosClient.post(`/products/${id}`, product);
}

export function deleteProduct({ commit }, id) {
  return axiosClient.delete(`/products/${id}`);
}

export async function getBentos({ commit }, { storeId, ...params }) {
  commit("setBentosLoading", true);
  try {
    const response = await axiosClient.get(`/bentos`, {
      params: { store_id: storeId, ...params } // Make sure store_id is sent
    });
    console.log("This is the response: ", response)
    commit("setBentosData", response.data); // Handle the response in Vuex
  } catch (error) {
    console.error("Error fetching bentos:", error);
  } finally {
    commit("setBentosLoading", false);
  }
}



export async function getStores({ commit }, params) {
  commit("setLoading", { entity: "stores", isLoading: true });  // Set loading to true before the request
  try {
    const token = localStorage.getItem('TOKEN');
    const response = await axiosClient.get("/stores", {
      params,
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });
    commit("setStores", [false, response.data]);  // Pass data and set loading to false
  } catch (error) {
    console.error("Error fetching stores:", error);
    commit("setLoading", { entity: "stores", isLoading: false });  // Set loading to false in case of error
  }
}





export async function createStore({ commit }, storeData) {
  try {
    const token = localStorage.getItem('TOKEN');
    
    console.log("FormData being sent:");
    for (let pair of storeData.entries()) {
      console.log(`${pair[0]}: ${pair[1]}`);
    }

    const response = await axiosClient.post('/stores', storeData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'multipart/form-data',
      },
    });

    commit('setStoreData', response.data);
    return response;
  } catch (error) {
    // Capture validation errors
    if (error.response && error.response.status === 422) {
      console.error("Validation errors:", error.response.data.errors);
    } else {
      console.error("Error creating store:", error);
    }
    throw error;
  }
}

export async function updateBentoDynamicFields({ commit }, { bentoId, dynamicFields }) {
  try {
    const response = await axiosClient.post(`/bentos/${bentoId}/update-dynamic`, dynamicFields);
    console.log("Bento dynamic fields updated:", response.data);
  } catch (error) {
    console.error("Error updating bento dynamic fields:", error);
  }
}








// Fetch single store (for editing)
export async function getStore({ commit }, id) {
  return axiosClient.get(`/stores/${id}`);
}

// Update store
export async function updateStore({ commit }, storeData) {
  // Log FormData contents for debugging
  console.log("FormData contents from inside updateStore:", [...storeData.entries()]);

  // Add the `_method` field to simulate a PUT request
  storeData.append('_method', 'PUT');

  try {
    // Change the request method to POST and let Laravel interpret it as a PUT request
    const response = await axiosClient.post(`/stores/${storeData.get('id')}`, storeData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    
    commit('setStoreData', response.data);  // Commits the updated store data
    return response;
  } catch (error) {
    console.error("Error updating store:", error);  // Catches any errors
    throw error;
  }
}







export function getUsers({ commit, state }, { url = null, search = '', per_page, sort_field, sort_direction } = {}) {
  commit('setUsers', [true]);
  url = url || '/users';
  const params = {
    per_page: state.users.limit,
  };
  return axiosClient.get(url, {
    params: {
      ...params,
      search, per_page, sort_field, sort_direction,
    }
  })
    .then((response) => {
      commit('setUsers', [false, response.data]);
    })
    .catch(() => {
      commit('setUsers', [false]);
    });
}

export function createUser({ commit }, user) {
  return axiosClient.post('/users', user);
}

export function updateUser({ commit }, user) {
  return axiosClient.put(`/users/${user.id}`, user);
}

export function getCustomers({ commit, state }, { url = null, search = '', per_page, sort_field, sort_direction } = {}) {
  commit('setCustomers', [true]);
  url = url || '/customers';
  const params = {
    per_page: state.customers.limit,
  };
  return axiosClient.get(url, {
    params: {
      ...params,
      search, per_page, sort_field, sort_direction,
    }
  })
    .then((response) => {
      commit('setCustomers', [false, response.data]);
    })
    .catch(() => {
      commit('setCustomers', [false]);
    });
}

export function getCustomer({ commit }, id) {
  return axiosClient.get(`/customers/${id}`);
}

export function createCustomer({ commit }, customer) {
  return axiosClient.post('/customers', customer);
}

export function updateCustomer({ commit }, customer) {
  return axiosClient.put(`/customers/${customer.id}`, customer);
}

export function deleteCustomer({ commit }, customer) {
  return axiosClient.delete(`/customers/${customer.id}`);
}

export function deleteUser({ commit }, user) {
  return axiosClient.delete(`/users/${user.id}`);
}

export async function deleteStore({ commit }, storeId) {
  try {
    const response = await axiosClient.delete(`/stores/${storeId}`);
    return response;
  } catch (error) {
    console.error("Error deleting store:", error);
    throw error;
  }
}

export async function deleteBento({ commit }, bentoId) {
  try {
    const response = await axiosClient.delete(`/bentos/${bentoId}`);
    commit('removeBento', bentoId);  // Commit a mutation to remove the bento from the state
    return response;
  } catch (error) {
    console.error("Error deleting bento:", error);
    throw error;  // Re-throw the error to be handled in the component
  }
}



export function getCategories({ commit, state }, { sort_field, sort_direction } = {}) {
  commit('setCategories', [true]);
  return axiosClient.get('/categories', {
    params: {
      sort_field, sort_direction,
    }
  })
    .then((response) => {
      commit('setCategories', [false, response.data]);
    })
    .catch(() => {
      commit('setCategories', [false]);
    });
}

export function createCategory({ commit }, category) {
  return axiosClient.post('/categories', category);
}

export function updateCategory({ commit }, category) {
  return axiosClient.put(`/categories/${category.id}`, category);
}

export function deleteCategory({ commit }, category) {
  return axiosClient.delete(`/categories/${category.id}`);
}
