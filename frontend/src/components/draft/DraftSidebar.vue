<template>
  <div class="sidebar">
    <div class="sidebar-header">
      <h2>Joueurs disponibles</h2>
      <span class="sidebar-count">{{ players.length }} joueurs</span>
    </div>

    <!-- Filtres -->
    <div class="sidebar-filters">
      <input
        v-model="search"
        class="input"
        type="text"
        placeholder="Rechercher un joueur..."
        @input="emitFilter"
      />
      <select v-model="positionFilter" class="select" @change="emitFilter">
        <option value="">Tous les postes</option>
        <option v-for="pos in positions" :key="pos.id" :value="pos.id">
          {{ pos.name }}
        </option>
      </select>
    </div>

    <!-- Liste -->
    <div class="sidebar-list">
      <div v-if="loading" class="sidebar-loading">Chargement...</div>

      <div v-else-if="players.length === 0" class="sidebar-empty">
        Aucun joueur disponible.<br/>Sélectionne une saison.
      </div>

      <PlayerCard
        v-else
        v-for="player in players"
        :key="player.id"
        :player="player"
        @select="onSelect"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import PlayerCard from './PlayerCard.vue'

defineProps({
  players:   { type: Array,   default: () => [] },
  positions: { type: Array,   default: () => [] },
  loading:   { type: Boolean, default: false },
})

const emit = defineEmits(['select', 'filter'])

const search         = ref('')
const positionFilter = ref('')

const emitFilter = () => {
  emit('filter', { search: search.value, position_id: positionFilter.value })
}

const onSelect = (player) => {
  // Demande le numéro de position via un prompt simple pour l'instant
  const num = parseInt(prompt(`Position sur le terrain pour ${player.name} (1-15) :`))
  if (num >= 1 && num <= 15) {
    emit('select', player, num)
  }
}
</script>

<style scoped>
.sidebar {
  background-color: #1a2634;
  border: 1px solid #1e3a5f;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  padding: 20px;
  height: calc(100vh - 140px);
  position: sticky;
  top: 80px;
}

.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.sidebar-header h2 {
  font-size: 1rem;
  font-weight: 600;
}

.sidebar-count {
  font-size: 0.8rem;
  color: #94a3b8;
}

.sidebar-filters {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.input, .select {
  background-color: #0a0a0a;
  color: #ffffff;
  border: 1px solid #334155;
  border-radius: 8px;
  padding: 10px 14px;
  font-size: 0.9rem;
  width: 100%;
}

.input::placeholder { color: #475569; }
.input:focus, .select:focus {
  outline: none;
  border-color: #cba233 ;
}

.sidebar-list {
  flex: 1;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding-right: 4px;
}

.sidebar-list::-webkit-scrollbar { width: 4px; }
.sidebar-list::-webkit-scrollbar-track { background: transparent; }
.sidebar-list::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }

.sidebar-loading, .sidebar-empty {
  color: #475569;
  text-align: center;
  padding: 40px 0;
  font-size: 0.9rem;
  line-height: 1.8;
}
</style>