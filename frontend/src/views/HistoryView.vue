<template>
  <div class="history">
    <h1>Mes simulations</h1>

    <div v-if="loading" class="state">Chargement...</div>

    <div v-else-if="drafts.length === 0" class="state">
      Aucune simulation pour l'instant.<br />
      <RouterLink to="/draft" class="link">Créer une draft →</RouterLink>
    </div>

    <div v-else class="drafts-grid">
      <div v-for="draft in drafts" :key="draft.id" class="draft-card">
        <div class="draft-card-header">
          <h3>{{ draft.name }}</h3>
          <span class="draft-season">{{ draft.season?.label || draft.season?.year }}</span>
        </div>
        <div class="draft-card-info">
          <span>{{ draft.players?.length || 0 }}/15 joueurs</span>
        </div>
        <div class="draft-card-actions">
          <RouterLink :to="`/draft/${draft.id}`" class="btn btn--secondary">
            Modifier
          </RouterLink>
          <RouterLink
            v-if="draft.players?.length === 15"
            :to="`/simulation/${draft.id}`"
            class="btn btn--primary"
          >
            ⚡ Simuler
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { draftService } from '../services/api'

const drafts  = ref([])
const loading = ref(true)

onMounted(async () => {
  const { data } = await draftService.getAll()
  drafts.value   = data
  loading.value  = false
})
</script>

<style scoped>
.history {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

h1 {
  font-size: 1.8rem;
  font-weight: 700;
}

.state {
  text-align: center;
  color: #64748b;
  padding: 60px 0;
  line-height: 2;
}

.link {
  color: #C9A84C ;
  text-decoration: none;
  font-weight: 500;
}

.drafts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 16px;
}

.draft-card {
  background-color: #1a2634;
  border: 1px solid #1e3a5f;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.draft-card-header {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.draft-card-header h3 {
  font-size: 1rem;
  font-weight: 600;
}

.draft-season {
  font-size: 0.8rem;
  color: #64748b;
}

.draft-card-info {
  font-size: 0.85rem;
  color: #94a3b8;
}

.draft-card-actions {
  display: flex;
  gap: 8px;
}

.btn {
  flex: 1;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 600;
  text-decoration: none;
  text-align: center;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}

.btn--primary {
  background-color: #C9A84C ;
  color: #ffffff;
}

.btn--primary:hover {
  background-color: #1d4ed8;
}

.btn--secondary {
  background-color: transparent;
  color: #ffffff;
  border: 1px solid #334155;
}

.btn--secondary:hover {
  border-color: #ffffff;
}
</style>