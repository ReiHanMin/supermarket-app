export default {
  user: {
    token: sessionStorage.getItem('TOKEN'),
    data: {}
  },
  products: {
    loading: false,
    data: [],
    links: [],
    from: null,
    to: null,
    page: 1,
    limit: null,
    total: null
  },
  bentos: {
    loading: false,       // Track loading state while fetching bentos
    data: [],             // Bento list data
    selectedBento: null,  // To hold data for a specific bento when editing/updating
    total: 0,
    from: 0,
    to: 0,
    limit: 20,
    links: []
  },
  stores: {
    loading: false,      // Track loading state while fetching stores
    data: [],            // Store list data
    total: 0,
  },
  users: {
    loading: false,
    data: [],
    links: [],
    from: null,
    to: null,
    page: 1,
    limit: null,
    total: null
  },
  customers: {
    loading: false,
    data: [],
    links: [],
    from: null,
    to: null,
    page: 1,
    limit: null,
    total: null
  },
  countries: [],
  orders: {
    loading: false,
    data: [],
    links: [],
    from: null,
    to: null,
    page: 1,
    limit: null,
    total: null
  },
  toast: {
    show: false,
    message: '',
    delay: 5000
  },
  dateOptions: [
    {key: '1d', text: 'Last Day'},
    {key: '1k', text: 'Last Week'},
    {key: '2k', text: 'Last 2 Weeks'},
    {key: '1m', text: 'Last Month'},
    {key: '3m', text: 'Last 3 Months'},
    {key: '6m', text: 'Last 6 Months'},
    {key: 'all', text: 'All Time'},
  ],
  categories: {
    loading: false,
    data: [],
  }
}
