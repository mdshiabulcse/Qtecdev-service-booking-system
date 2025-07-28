import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/store/auth'

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('@/views/Home.vue')
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/auth/Login.vue'),
    meta: { guestOnly: true }
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/views/auth/Register.vue'),
    meta: { guestOnly: true }
  },
  {
    path: '/services',
    name: 'services',
    component: () => import('@/views/services/ServicesList.vue')
  },
  {
    path: '/services/new',
    name: 'service-create',
    component: () => import('@/views/services/ServiceForm.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/services/:id/edit',
    name: 'service-edit',
    component: () => import('@/views/services/ServiceForm.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
    props: true
  },
  {
    path: '/bookings',
    name: 'bookings',
    component: () => import('@/views/bookings/BookingsList.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/bookings/new',
    name: 'booking-create',
    component: () => import('@/views/bookings/CreateBooking.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/bookings/new/:serviceId',
    name: 'service-booking-create',
    component: () => import('@/views/bookings/CreateBooking.vue'),
    meta: { requiresAuth: true },
    props: true
  },
  {
    path: '/admin',
    name: 'admin',
    component: () => import('@/views/AdminDashboard.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/NotFound.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to) => {
  const authStore = useAuthStore()

  if (!authStore.user && authStore.token) {
    await authStore.init()
  }

  if (to.meta.requiresAuth && !authStore.token) {
    return '/login'
  }

  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    return '/'
  }

  if (to.meta.guestOnly && authStore.token) {
    return '/'
  }
})

export default router
