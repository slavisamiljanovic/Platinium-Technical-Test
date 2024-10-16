import {
  createRouter,
  createWebHistory,
  RouteRecordRaw
} from 'vue-router'
import {
  AdminDashboardView,
  HomeView,
  AdminLoginView
} from '@/views'
import store from '@/store'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
    meta: {
      title: 'Home',
      requiresAuth: false
    }
  },
  {
    path: '/login',
    name: 'login',
    component: AdminLoginView,
    meta: {
      title: 'Login',
      requiresAuth: false
    }
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: AdminDashboardView,
    meta: {
      title: 'Admin Dashboard',
      requiresAuth: true
    }
  },
  {
    path: '/tickets/:currentPage?',
    name: 'tickets',
    component: () => import(/* webpackChunkName: "events" */ '@/views/tickets/TicketsListView.vue'),
    meta: {
      title: 'Manage Tickets',
      requiresAuth: true
    }
  },
  {
    path: '/events',
    name: 'events',
    component: () => import(/* webpackChunkName: "events" */ '@/views/tickets/TicketsListView.vue'),
    meta: {
      title: 'Manage Events',
      requiresAuth: true
    }
  },
  {
    path: '/organisers',
    name: 'organisers',
    component: () => import(/* webpackChunkName: "events" */ '@/views/tickets/TicketsListView.vue'),
    meta: {
      title: 'Manage Organisers',
      requiresAuth: true
    }
  },
  {
    path: '/about',
    name: 'about',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/AboutView.vue'),
    meta: {
      title: 'About',
      requiresAuth: false
    }
  },
  {
    path: '/:catchAll(.*)',
    redirect: '/' // Redirect to home page if not found.
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

// Global before guard.
router.beforeEach(
  (to, from, next) => {
    const defaultTitle = 'Technical Test - Tickets System'
    document.title = (to.meta?.title as string) + ' | ' + defaultTitle || defaultTitle
    if (to.matched.some(record => record.meta.requiresAuth)) {
      if (store.getters.isLoggedIn) {
        next()
        return
      }
      next('/login')
    } else if (to.matched.some(record => record.path === '/login')) {
      if (store.getters.isLoggedIn) {
        next('/dashboard')
        return
      }
      next()
    } else {
      next()
    }
  }
)

export default router
