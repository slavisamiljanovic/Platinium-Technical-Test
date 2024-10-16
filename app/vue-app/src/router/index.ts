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
import { useHelper, TokenExpiration } from '@/store/helpers'

// Destructure methods from the helper.
const {
  isLoggedIn,
  isTokenExpired,
  isTokenAboutToExpire
} = useHelper()

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
    path: '/tickets/:currentPage',
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

    const token = store.state.token
    const isUserLoggedIn = isLoggedIn(token)
    const isUserTokenExpired = isTokenExpired(token)
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
    const isLoginPage = to.matched.some(record => record.path === '/login')

    // A case to refresh the token immediately if it is about to expire.
    const tokenExpiration: TokenExpiration = isTokenAboutToExpire(token)
    if (tokenExpiration.isAboutToExpire) {
      store.dispatch('refreshToken')
    }

    // A case for removing a token when it has expired and notifying the user.
    if (token && isUserTokenExpired) {
      store.dispatch('tokenExpired')
    }

    if (requiresAuth || isLoginPage) {
      // Case for redirecting to appropriate pages based on token state.
      if (!isUserLoggedIn && requiresAuth) {
        next('/login')
      } else if (isUserLoggedIn && isLoginPage) {
        next('/dashboard')
      } else {
        next()
      }
    } else {
      next()
    }
  }
)

export default router
