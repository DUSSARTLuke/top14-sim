<template>
  <div class="auth">
    <div class="auth-card">
      <div class="auth-tabs">
        <button
          class="auth-tab"
          :class="{ 'auth-tab--active': mode === 'login' }"
          @click="mode = 'login'"
        >
          Connexion
        </button>
        <button
          class="auth-tab"
          :class="{ 'auth-tab--active': mode === 'register' }"
          @click="mode = 'register'"
        >
          Inscription
        </button>
      </div>

      <form class="auth-form" @submit.prevent="handleSubmit">
        <div v-if="mode === 'register'" class="form-group">
          <label>Nom</label>
          <input v-model="form.name" type="text" class="input" placeholder="Ton nom" required />
        </div>

        <div class="form-group">
          <label>Email</label>
          <input v-model="form.email" type="email" class="input" placeholder="ton@email.com" required />
        </div>

        <div class="form-group">
          <label>Mot de passe</label>
          <input v-model="form.password" type="password" class="input" placeholder="••••••••" required />
        </div>

        <div v-if="mode === 'register'" class="form-group">
          <label>Confirmer le mot de passe</label>
          <input v-model="form.password_confirmation" type="password" class="input" placeholder="••••••••" required />
        </div>

        <div v-if="error" class="auth-error">{{ error }}</div>

        <button type="submit" class="btn btn--primary" :disabled="loading">
          {{ loading ? 'Chargement...' : mode === 'login' ? 'Se connecter' : "S'inscrire" }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { authService } from '../services/api'

const router = useRouter()
const mode   = ref('login')
const loading = ref(false)
const error   = ref('')

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const handleSubmit = async () => {
  loading.value = true
  error.value   = ''

  try {
    const { data } = mode.value === 'login'
      ? await authService.login({ email: form.email, password: form.password })
      : await authService.register(form)

    localStorage.setItem('auth_token', data.token)
    router.push('/')
  } catch (e) {
    error.value = e.response?.data?.message || 'Une erreur est survenue.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.auth {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 70vh;
}

.auth-card {
  background-color: #1a2634;
  border: 1px solid #1e3a5f;
  border-radius: 16px;
  padding: 40px;
  width: 100%;
  max-width: 420px;
  display: flex;
  flex-direction: column;
  gap: 28px;
}

.auth-tabs {
  display: flex;
  gap: 0;
  background-color: #0f1923;
  border-radius: 8px;
  padding: 4px;
}

.auth-tab {
  flex: 1;
  padding: 10px;
  border: none;
  background: none;
  color: #64748b;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.2s;
}

.auth-tab--active {
  background-color: #2563eb;
  color: #ffffff;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-group label {
  font-size: 0.85rem;
  color: #94a3b8;
}

.input {
  background-color: #0f1923;
  color: #ffffff;
  border: 1px solid #334155;
  border-radius: 8px;
  padding: 12px 14px;
  font-size: 0.95rem;
  width: 100%;
}

.input:focus {
  outline: none;
  border-color: #2563eb;
}

.auth-error {
  background-color: #450a0a;
  border: 1px solid #991b1b;
  color: #fca5a5;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 0.85rem;
}

.btn {
  padding: 12px;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all 0.2s;
  width: 100%;
}

.btn--primary {
  background-color: #2563eb;
  color: #ffffff;
}

.btn--primary:hover:not(:disabled) {
  background-color: #1d4ed8;
}

.btn--primary:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
</style>