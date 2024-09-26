import { createRouter, createWebHistory } from "vue-router";
import AppLayout from '../components/AppLayout.vue'
import Login from "../views/Login.vue";
import Dashboard from "../views/Dashboard.vue";
import Bentos from "../views/Bentos/Bentos.vue"; // Updated from Products to Bentos
import BentoView from "../views/Bentos/BentoView.vue"; // Import the BentoView component
import Users from "../views/Users/Users.vue";
import Customers from "../views/Customers/Customers.vue";
import CustomerView from "../views/Customers/CustomerView.vue";
import Stores from "../views/Stores/Stores.vue"; // Add Stores page
import StoreForm from "../views/Stores/StoreForm.vue"; // Import StoreForm component
import RequestPassword from "../views/RequestPassword.vue";
import ResetPassword from "../views/ResetPassword.vue";
import NotFound from "../views/NotFound.vue";
import store from "../store";
import Report from "../views/Reports/Report.vue";
import CustomersReport from "../views/Reports/CustomersReport.vue";
import StoresReport from "../views/Reports/StoresReport.vue";  // Import StoresReport component
import BentoForm from "../views/Bentos/BentoForm.vue"; // Updated from ProductForm to BentoForm
import Categories from "../views/Categories/Categories.vue";

const routes = [
  {
    path: '/',
    redirect: '/app'
  },
  {
    path: '/app',
    name: 'app',
    redirect: '/app/dashboard',
    component: AppLayout,
    meta: {
      requiresAuth: true
    },
    children: [
      {
        path: 'dashboard',
        name: 'app.dashboard',
        component: Dashboard
      },
      {
        path: 'bentos',
        name: 'app.bentos',
        component: Bentos // Updated from Products to Bentos
      },
      {
        path: 'categories',
        name: 'app.categories',
        component: Categories
      },
      {
        path: 'bentos/create',
        name: 'app.bentos.create',
        component: BentoForm // Updated from ProductForm to BentoForm
      },
      {
        path: 'bentos/:id/edit',
        name: 'app.bentos.edit',
        component: BentoForm // Updated from ProductForm to BentoForm
      },
      {
        path: 'bentos/:id',
        name: 'app.bentos.view', // Updated from products to bentos
        component: BentoView, // Updated to BentoView component
        props: true
      },
      {
        path: 'users',
        name: 'app.users',
        component: Users
      },
      {
        path: 'customers',
        name: 'app.customers',
        component: Customers
      },
      {
        path: 'customers/:id',
        name: 'app.customers.view',
        component: CustomerView
      },

      {
        path: 'stores',  // Add Stores path
        name: 'app.stores',
        component: Stores
      },
      {
        path: 'stores/create', // Add Create Store path
        name: 'app.stores.create',
        component: StoreForm
      },
      {
        path: 'stores/:id',  // Add Store editing route
        name: 'edit-store',
        component: StoreForm,
        props: true
      },
      {
        path: '/report',
        name: 'reports',
        component: Report,
        meta: {
          requiresAuth: true
        },
        children: [
   
          {
            path: 'customers/:date?',
            name: 'reports.customers',
            component: CustomersReport
          },
          {
            path: 'stores/:date?',  // Add Stores report route
            name: 'reports.stores',
            component: StoresReport
          }
        ]
      },
    ]
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: {
      requiresGuest: true
    }
  },
  {
    path: '/request-password',
    name: 'requestPassword',
    component: RequestPassword,
    meta: {
      requiresGuest: true
    }
  },
  {
    path: '/reset-password/:token',
    name: 'resetPassword',
    component: ResetPassword,
    meta: {
      requiresGuest: true
    }
  },
  {
    path: '/:pathMatch(.*)',
    name: 'notfound',
    component: NotFound,
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !store.state.user.token) {
    next({name: 'login'})
  } else if (to.meta.requiresGuest && store.state.user.token) {
    next({name: 'app.dashboard'})
  } else {
    next();
  }
})

export default router;
