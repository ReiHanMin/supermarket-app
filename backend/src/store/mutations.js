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

export function setBentosData(state, data) {
  state.bentos = {
    ...state.bentos, // Preserve other properties like loading, total, etc.
    data: data.map(bento => ({
      id: bento.id,
      name: bento.name,
      description: bento.description,
      price: bento.price,
      status: bento.status,
      store_id: bento.store_id,
      created_at: bento.created_at,
      updated_at: bento.updated_at,
      image_url: bento.image_url,
      ingredients: bento.ingredients,
      calories: bento.calories,
      discount_percentage: bento.discount_percentage,
      availability: bento.availability,
      rating: bento.rating,
      reviews_count: bento.reviews_count,
    })),
    total: data.length, // Update total count if necessary
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
