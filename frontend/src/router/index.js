import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('@/views/HomeView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/LoginView.vue'),
      meta: { guest: true }
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/views/RegisterView.vue'),
      meta: { guest: true }
    },
    {
      path: '/settings',
      name: 'settings',
      component: () => import('@/views/SettingsView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/reviews',
      name: 'reviews',
      component: () => import('@/views/ReviewsView.vue'),
      meta: { requiresAuth: true }
    }
  ]
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  // Загружаем пользователя, если ещё не загружен
  if (!authStore.user) {
    await authStore.fetchUser()
  }

  if (to.meta.requiresAuth && !authStore.user) {
    next({ name: 'login' })
  } else if (to.meta.guest && authStore.user) {
    next({ name: 'home' })
  } else {
    next()
  }
})

export default router
