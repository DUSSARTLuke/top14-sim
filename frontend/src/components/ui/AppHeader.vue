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
  background-color: #0a0a0a;
  border-bottom: 1px solid #C9A84C;
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
  font-size: 1.3rem;
  font-weight: 900;
  color: #ffffff;
  text-decoration: none;
  letter-spacing: -0.5px;
  text-transform: uppercase;
}

.logo span {
  color: #C9A84C;
}

.nav {
  display: flex;
  align-items: center;
  gap: 24px;
}

.nav-link {
  color: #64748b;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
  transition: color 0.2s;
  background: none;
  border: none;
  cursor: pointer;
  font-family: inherit;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.nav-link:hover,
.nav-link.router-link-active {
  color: #ffffff;
}

.nav-link--cta {
  background: transparent;
  color: #C9A84C !important;
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: 600;
  border: 1px solid #C9A84C;
}

.nav-link--cta:hover {
  background-color: #C9A84C;
  color: #000000 !important;
}
</style>