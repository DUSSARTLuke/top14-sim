<template>
  <div class="simul">
    <!-- Scoreboard -->
    <div class="scoreboard">
      <div class="team-score">
        <span class="team-name">{{ homeDraft?.name || 'Mon équipe' }}</span>
        <span class="score">{{ simulStore.scoreHome }}</span>
      </div>

      <div class="match-info">
        <div class="minute">{{ simulStore.isFinished ? 'FIN' : simulStore.minute + "'" }}</div>
        <div class="match-status">{{ statusLabel }}</div>
      </div>

      <div class="team-score team-score--away">
        <span class="score">{{ simulStore.scoreAway }}</span>
        <span class="team-name">{{ awayDraft?.name || 'Adversaire' }}</span>
      </div>
    </div>

    <!-- Sélection adversaire -->
    <div v-if="!awayDraft && !simulStore.isRunning" class="opponent-select">
      <h2>Choisir un adversaire</h2>
      <p>Sélectionne une autre draft pour simuler le match.</p>
      <div class="drafts-list">
        <div
          v-for="draft in availableDrafts"
          :key="draft.id"
          class="draft-option"
          @click="selectOpponent(draft)"
        >
          <span class="draft-option-name">{{ draft.name }}</span>
          <span class="draft-option-season">{{ draft.season?.label || draft.season?.year }}</span>
        </div>
      </div>
    </div>

    <!-- Contrôles -->
    <div v-if="awayDraft" class="controls">
      <button
        v-if="!simulStore.isRunning && !simulStore.isFinished"
        class="btn btn--primary btn--large"
        @click="startSimulation"
      >
        ⚡ Lancer la simulation
      </button>
      <button
        v-if="simulStore.isFinished"
        class="btn btn--secondary"
        @click="resetSimulation"
      >
        🔄 Rejouer
      </button>
      <RouterLink to="/draft" class="btn btn--secondary">
        ✏️ Modifier la draft
      </RouterLink>
    </div>

    <!-- Match log + terrain -->
    <div v-if="simulStore.matchEvents.length > 0" class="simul-content">
      <!-- Timeline des événements -->
      <div class="match-log">
        <h3>Déroulement du match</h3>
        <div class="events" ref="eventsContainer">
          <div
            v-for="(event, i) in simulStore.matchEvents"
            :key="i"
            class="event"
            :class="`event--${event.team}`"
          >
            <span class="event-minute">{{ event.minute }}'</span>
            <span class="event-icon">{{ eventIcon(event.type) }}</span>
            <span class="event-desc">{{ event.description }}</span>
          </div>
        </div>
      </div>

      <!-- Stats fin de match -->
      <div v-if="simulStore.isFinished" class="match-stats">
        <h3>Statistiques</h3>
        <div class="stat-row" v-for="stat in matchStats" :key="stat.label">
          <span class="stat-value">{{ stat.home }}</span>
          <span class="stat-label">{{ stat.label }}</span>
          <span class="stat-value">{{ stat.away }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSimulStore } from '../stores/useSimulStore'
import { draftService } from '../services/api'

const route      = useRoute()
const router     = useRouter()
const simulStore = useSimulStore()

const homeDraft       = ref(null)
const awayDraft       = ref(null)
const availableDrafts = ref([])
const eventsContainer = ref(null)

const statusLabel = computed(() => {
  if (simulStore.isFinished) return 'Match terminé'
  if (simulStore.isRunning)  return 'En cours...'
  return 'Pas encore commencé'
})

const matchStats = computed(() => [
  { label: 'Essais',    home: countEvents('try', 'home'),     away: countEvents('try', 'away') },
  { label: 'Pénalités', home: countEvents('penalty', 'home'), away: countEvents('penalty', 'away') },
  { label: 'Transformations', home: countEvents('conversion', 'home'), away: countEvents('conversion', 'away') },
])

const countEvents = (type, team) =>
  simulStore.matchEvents.filter(e => e.type === type && e.team === team).length

onMounted(async () => {
  simulStore.reset()

  // Charger la draft home
  const { data } = await draftService.getOne(route.params.draftId)
  homeDraft.value = data

  // Charger les autres drafts pour choisir l'adversaire
  const { data: all } = await draftService.getAll()
  availableDrafts.value = all.filter(d =>
    d.id != route.params.draftId && d.players?.length === 15
  )
})

// Auto-scroll sur les nouveaux événements
watch(() => simulStore.matchEvents.length, async () => {
  await nextTick()
  if (eventsContainer.value) {
    eventsContainer.value.scrollTop = eventsContainer.value.scrollHeight
  }
})

const selectOpponent = (draft) => {
  awayDraft.value = draft
  simulStore.setDrafts(homeDraft.value, draft)
}

const startSimulation = () => {
  simulStore.isRunning.value = true
  runSimulation()
}

const resetSimulation = () => {
  simulStore.reset()
  awayDraft.value = null
}

// Moteur de simulation côté frontend (simplifié)
const runSimulation = async () => {
  simulStore.isRunning = true
  const home = homeDraft.value
  const away = awayDraft.value

  // Calcul des forces des équipes
  const homeStrength = calcStrength(home.players)
  const awayStrength = calcStrength(away.players)
  const total        = homeStrength + awayStrength

  let scoreHome = 0
  let scoreAway = 0

  const events = generateEvents(home, away, homeStrength, awayStrength, total)

  // Rejouer les événements minute par minute
  for (const event of events) {
    await delay(600)
    simulStore.minute = event.minute

    if (event.team === 'home') {
      scoreHome += event.points
    } else {
      scoreAway += event.points
    }

    simulStore.addEvent(event)
    simulStore.updateScore(scoreHome, scoreAway)
  }

  await delay(800)
  simulStore.minute    = 80
  simulStore.isRunning = false
  simulStore.isFinished = true
}

const calcStrength = (players) => {
  if (!players?.length) return 50
  return players.reduce((acc, p) => acc + (p.rating || 50), 0) / players.length
}

const generateEvents = (home, away, homeStr, awayStr, total) => {
  const events   = []
  const minutes  = []

  // Générer des minutes aléatoires pour les actions
  const nbEvents = 8 + Math.floor(Math.random() * 8) // 8 à 15 actions
  for (let i = 0; i < nbEvents; i++) {
    minutes.push(Math.floor(Math.random() * 78) + 1)
  }
  minutes.sort((a, b) => a - b)

  for (const minute of minutes) {
    // Probabilité selon la force de l'équipe
    const isHome = Math.random() < (homeStr / total)
    const team   = isHome ? 'home' : 'away'
    const draft  = isHome ? home : away

    // Type d'action aléatoire pondéré
    const rand = Math.random()
    let type, points, description

    const scorer = randomPlayer(draft.players)

    if (rand < 0.45) {
      type        = 'try'
      points      = 5
      description = `Essai de ${scorer} !`
    } else if (rand < 0.65) {
      type        = 'conversion'
      points      = 2
      description = `Transformation réussie par ${scorer}`
    } else if (rand < 0.85) {
      type        = 'penalty'
      points      = 3
      description = `Pénalité pour ${draft.name} par ${scorer}`
    } else {
      type        = 'drop'
      points      = 3
      description = `Drop de ${scorer} !`
    }

    events.push({ minute, team, type, points, description,
      teamName: draft.name })
  }

  return events
}

const randomPlayer = (players) => {
  if (!players?.length) return 'Joueur inconnu'
  const p = players[Math.floor(Math.random() * players.length)]
  return p.name
}

const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms))

const eventIcon = (type) => {
  const icons = { try: '🏉', conversion: '🎯', penalty: '⚽', drop: '👟', card: '🟨' }
  return icons[type] || '📌'
}
</script>

<style scoped>
.simul {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

/* Scoreboard */
.scoreboard {
  background: linear-gradient(135deg, #1a2634, #0a0a0a);
  border: 1px solid #1e3a5f;
  border-radius: 16px;
  padding: 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.team-score {
  display: flex;
  flex-direction: column;
  gap: 8px;
  flex: 1;
}

.team-score--away {
  align-items: flex-end;
}

.team-name {
  font-size: 1rem;
  color: #94a3b8;
  font-weight: 500;
}

.score {
  font-size: 4rem;
  font-weight: 800;
  color: #ffffff;
  line-height: 1;
}

.match-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  min-width: 120px;
}

.minute {
  font-size: 1.8rem;
  font-weight: 700;
  color: #C9A84C ;
}

.match-status {
  font-size: 0.8rem;
  color: #64748b;
}

/* Opponent select */
.opponent-select {
  background-color: #1a2634;
  border: 1px solid #1e3a5f;
  border-radius: 12px;
  padding: 28px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.opponent-select h2 {
  font-size: 1.2rem;
  font-weight: 600;
}

.opponent-select p {
  color: #64748b;
  font-size: 0.9rem;
}

.drafts-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 12px;
}

.draft-option {
  background-color: #0a0a0a;
  border: 1px solid #334155;
  border-radius: 8px;
  padding: 16px;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  gap: 4px;
  transition: all 0.2s;
}

.draft-option:hover {
  border-color: #cba233 ;
  background-color: #1a2634;
}

.draft-option-name {
  font-weight: 600;
  font-size: 0.9rem;
}

.draft-option-season {
  font-size: 0.75rem;
  color: #64748b;
}

/* Contrôles */
.controls {
  display: flex;
  gap: 12px;
}

.btn {
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  border: none;
  text-decoration: none;
  transition: all 0.2s;
}

.btn--large {
  padding: 16px 40px;
  font-size: 1.1rem;
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

/* Contenu simulation */
.simul-content {
  display: grid;
  grid-template-columns: 1fr 320px;
  gap: 24px;
  align-items: start;
}

/* Match log */
.match-log {
  background-color: #1a2634;
  border: 1px solid #1e3a5f;
  border-radius: 12px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.match-log h3 {
  font-size: 1rem;
  font-weight: 600;
}

.events {
  display: flex;
  flex-direction: column;
  gap: 8px;
  max-height: 400px;
  overflow-y: auto;
  padding-right: 4px;
}

.events::-webkit-scrollbar { width: 4px; }
.events::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }

.event {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 0.88rem;
  animation: slideIn 0.3s ease;
}

.event--home {
  background-color: rgba(37, 99, 235, 0.15);
  border-left: 3px solid #C9A84C ;
}

.event--away {
  background-color: rgba(220, 38, 38, 0.15);
  border-left: 3px solid #dc2626;
}

.event-minute {
  font-weight: 700;
  color: #94a3b8;
  min-width: 32px;
  font-size: 0.8rem;
}

.event-icon { font-size: 1rem; }

.event-desc { flex: 1; }

@keyframes slideIn {
  from { opacity: 0; transform: translateY(-8px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Match stats */
.match-stats {
  background-color: #1a2634;
  border: 1px solid #1e3a5f;
  border-radius: 12px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.match-stats h3 {
  font-size: 1rem;
  font-weight: 600;
  text-align: center;
}

.stat-row {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  gap: 12px;
}

.stat-value {
  font-size: 1.2rem;
  font-weight: 700;
  color: #ffffff;
}

.stat-value:first-child { text-align: left; }
.stat-value:last-child  { text-align: right; }

.stat-label {
  font-size: 0.8rem;
  color: #64748b;
  text-align: center;
  white-space: nowrap;
}
</style>