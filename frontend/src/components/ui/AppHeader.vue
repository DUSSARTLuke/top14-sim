<template>
  <header class="header">
    <div class="header-inner">
      <RouterLink to="/" class="logo">
        🏉 Top14 Sim
      </RouterLink>

      <nav class="nav">
        <RouterLink to="/" class="nav-link">Accueil</RouterLink>
        <RouterLink to="/draft" class="nav-link">Nouvelle draft</RouterLink>
        <RouterLink v-if="isLoggedIn" to="/history" class="nav-link">Mes simulations</RouterLink>
        <RouterLink v-if="!isLoggedIn" to="/login" class="nav-link nav-link--cta">Connexion</RouterLink>
        <button v-else class="nav-link nav-link--cta" @click="handleLogout">Déconnexion</button>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { authService } from '../../services/api'

const router  = useRouter()
const isLoggedIn = computed(() => !!localStorage.getItem('auth_token'))

const handleLogout = async () => {
  await authService.logout()
  localStorage.removeItem('auth_token')
  router.push('/')
}
</script>

<style scoped>
.header {
  background-color: #1a2634;
  border-bottom: 2px solid #2563eb;
  padding: 0 24px;
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-inner {
  max-width: 1280px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 64px;
}

.logo {
  font-size: 1.4rem;
  font-weight: 700;
  color: #ffffff;
  text-decoration: none;
  letter-spacing: 0.5px;
}

.nav {
  display: flex;
  align-items: center;
  gap: 24px;
}

.nav-link {
  color: #94a3b8;
  text-decoration: none;
  font-size: 0.95rem;
  transition: color 0.2s;
  background: none;
  border: none;
  cursor: pointer;
  font-family: inherit;
}

.nav-link:hover,
.nav-link.router-link-active {
  color: #ffffff;
}

.nav-link--cta {
  background-color: #2563eb;
  color: #ffffff !important;
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: 500;
}

.nav-link--cta:hover {
  background-color: #1d4ed8;
}
</style>