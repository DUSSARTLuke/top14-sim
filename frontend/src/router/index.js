import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/draft',
      name: 'draft',
      component: () => import('../views/DraftView.vue'),
    },
    {
      path: '/draft/:id',
      name: 'draft-edit',
      component: () => import('../views/DraftView.vue'),
    },
    {
      path: '/simulation/:draftId',
      name: 'simulation',
      component: () => import('../views/SimulationView.vue'),
    },
    {
      path: '/history',
      name: 'history',
      meta: { requiresAuth: true },
      component: () => import('../views/HistoryView.vue'),
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
    },
  ],
})

// Guard — redirige vers login si page protégée
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !localStorage.getItem('auth_token')) {
    next({ name: 'login' })
  } else {
    next()
  }
})

export default router