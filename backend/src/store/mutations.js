export function setUser(state, user) {
  state.user.data = user;
}

export function setToken(state, token) {
  state.user.token = token;
  if (token) {
    sessionStorage.setItem('TOKEN', token);
  } else {
    sessionStorage.removeItem('TOKEN');
  }
}

export function setProducts(state, [loading, data = null]) {
  if (data) {
    state.products = {
      ...state.products,
      data: data.data,
      links: data.meta?.links,
      page: data.meta.current_page,
      limit: data.meta.per_page,
      from: data.meta.from,
      to: data.meta.to,
      total: data.meta.total,
    };
  }
  state.products.loading = loading;
}

// Bentos Mutations
export function setBentosLoading(state, loading) {
  state.bentos.loading = loading;
}

export function setBentosData(state, response) {
  // Check if response contains a 'data' array (pagination format)
  const bentosData = response.data || response;

  // Update state.bentos with the correct data structure
  state.bentos = {
    ...state.bentos, // Preserve other properties like loading, total, etc.
    data: bentosData.map(bento => ({
      id: bento.id,
      name: bento.name,
      description: bento.description,
      price: bento.price,
      original_price: bento.original_price,
      usual_discount_percentage: bento.usual_discount_percentage,
      usual_discounted_price: bento.usual_discounted_price,
      estimated_discount_time: bento.estimated_discount_time,
      discount_status: bento.discount_status,
      stock_message: bento.stock_message,
      average_rating: bento.average_rating,
      total_reviews: bento.total_reviews,
      status: bento.status,
      image_url: bento.image_url,
      ingredients: bento.ingredients,
      calories: bento.calories,
      discount_percentage: bento.discount_percentage,
      availability: bento.availability,
      rating: bento.rating,
      reviews_count: bento.reviews_count,
      store_id: bento.store_id,
      store_name: bento.store_name, // Added store name
      chain_name: bento.chain_name, // Added chain name
      created_at: bento.created_at,
      updated_at: bento.updated_at,
    })),
    total: response.total || bentosData.length, // Get total from response if available, otherwise use the length
    from: response.from || 0,
    to: response.to || bentosData.length,
    limit: response.per_page || state.bentos.limit,
    links: response.links || [], // Update pagination links if available
  };
}


export function setStores(state, [loading, stores = null]) {
  state.stores.loading = loading;
  if (stores) {
    state.stores.data = stores.data || [];
    state.stores.meta = stores.meta || {};
  }
}

export function setStore(state, store) {
  state.store = store;
}




export function addStore(state, store) {
  state.stores.data.push(store);  // Assuming you have a stores array in your state
}

export function setStoreData(state, store) {
  state.storeData = {
    ...state.storeData,
    ...store,
  };
}





export function setUsers(state, [loading, data = null]) {
  if (data) {
    state.users = {
      ...state.users,
      data: data.data,
      links: data.meta?.links,
      page: data.meta.current_page,
      limit: data.meta.per_page,
      from: data.meta.from,
      to: data.meta.to,
      total: data.meta.total,
    };
  }
  state.users.loading = loading;
}

export function setCustomers(state, [loading, data = null]) {
  if (data) {
    state.customers = {
      ...state.customers,
      data: data.data,
      links: data.meta?.links,
      page: data.meta.current_page,
      limit: data.meta.per_page,
      from: data.meta.from,
      to: data.meta.to,
      total: data.meta.total,
    };
  }
  state.customers.loading = loading;
}

export function setOrders(state, [loading, data = null]) {
  if (data) {
    state.orders = {
      ...state.orders,
      data: data.data,
      links: data.meta?.links,
      page: data.meta.current_page,
      limit: data.meta.per_page,
      from: data.meta.from,
      to: data.meta.to,
      total: data.meta.total,
    };
  }
  state.orders.loading = loading;
}

export function showToast(state, message) {
  state.toast.show = true;
  state.toast.message = message;
}

export function hideToast(state) {
  state.toast.show = false;
  state.toast.message = '';
}

export function setCountries(state, countries) {
  state.countries = countries.data;
}

export function setCategories(state, [loading, data = null]) {
  if (data) {
    state.categories = {
      ...state.categories,
      data: data.data,
    };
  }
  state.categories.loading = loading;
}
