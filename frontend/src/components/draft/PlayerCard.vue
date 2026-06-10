<template>
  <div class="card" @click="$emit('select', player)">
    <div class="card-left">
      <div class="card-rating" :class="ratingClass">{{ player.rating }}</div>
    </div>
    <div class="card-info">
      <span class="card-name">{{ player.name }}</span>
      <span class="card-meta">{{ primaryPosition }} · {{ player.team?.name }}</span>
    </div>
    <div class="card-stats">
      <span title="Attaque">⚔ {{ player.attack }}</span>
      <span title="Défense">🛡 {{ player.defense }}</span>
      <span title="Vitesse">⚡ {{ player.speed }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  player: { type: Object, required: true }
})

defineEmits(['select'])

const primaryPosition = computed(() => {
  return props.player.positions?.find(p => p.pivot?.type === 'primary')?.name || '—'
})

const ratingClass = computed(() => {
  if (props.player.rating >= 85) return 'rating--gold'
  if (props.player.rating >= 70) return 'rating--silver'
  return 'rating--bronze'
})
</script>

<style scoped>
.card {
  display: flex;
  align-items: center;
  gap: 12px;
  background-color: #0a0a0a;
  border: 1px solid #1e3a5f;
  border-radius: 8px;
  padding: 10px 12px;
  cursor: pointer;
  transition: all 0.15s;
}

.card:hover {
  border-color: #cba233 ;
  background-color: #1a2634;
  transform: translateX(2px);
}

.card-rating {
  width: 36px;
  height: 36px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 0.9rem;
}

.rating--gold   { background-color: #78350f; color: #fbbf24; }
.rating--silver { background-color: #1e3a5f; color: #93c5fd; }
.rating--bronze { background-color: #1c1917; color: #a8a29e; }

.card-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
}

.card-name {
  font-size: 0.88rem;
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.card-meta {
  font-size: 0.75rem;
  color: #64748b;
}

.card-stats {
  display: flex;
  flex-direction: column;
  gap: 2px;
  font-size: 0.7rem;
  color: #94a3b8;
  text-align: right;
}
</style>