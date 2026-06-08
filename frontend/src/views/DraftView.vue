<template>
  <div class="draft">
    <div class="draft-header">
      <h1>{{ draft.name || 'Nouvelle draft' }}</h1>
      <button class="btn btn--primary" :disabled="(draftStore.players?.length ?? 0) < 15" @click="goToSimulation">
        ⚡ Simuler le match
      </button>
    </div>

    <div class="draft-content">
      <!-- Terrain -->
      <div class="draft-left">
        <FieldView :draft-players="draftStore.players ?? []" @remove="removePlayer" />
        <div class="draft-info">
          <span>{{ draftStore.players?.length ?? 0 }}/15 joueurs sélectionnés</span>
          <span v-if="(draftStore.players?.length ?? 0) === 15" class="ready">✅ Équipe complète !</span>
        </div>
      </div>

      <!-- Panel Roll -->
      <div class="draft-right">
        <div class="roll-panel">

          <!-- Bouton Roll -->
          <button
            class="btn-roll"
            :disabled="rolling || loading || (draftStore.players?.length ?? 0) >= 15"
            @click="rollTeam"
          >
            <span v-if="rolling" class="roll-spinning">⚽</span>
            <span v-else>🎲</span>
            {{ rolling ? 'Roll en cours...' : 'Roll' }}
          </button>

          <!-- Équipe rollée -->
          <div v-if="rolledTeam" class="rolled-team">
            <div class="rolled-team-header">
              <span class="rolled-team-name">{{ rolledTeam.name }}</span>
              <span class="rolled-team-season">{{ rolledTeam.season }}</span>
            </div>

            <div class="rolled-players" :class="{ 'rolled-players--locked': hasPickedFromRoll }">
  <div
    v-for="player in rolledPlayers"
    :key="player.id"
    class="rolled-player"
    :class="{
      'rolled-player--disabled': isPlayerDisabled(player) || (hasPickedFromRoll && !isPlayerSelected(player)),
      'rolled-player--selected': isPlayerSelected(player)
    }"
    @click="!isPlayerDisabled(player) && !hasPickedFromRoll && selectPlayer(player)"
  >
                <div class="rp-rating" :class="ratingClass(player.rating)">
                  {{ player.rating }}
                </div>
                <div class="rp-info">
                  <span class="rp-name">{{ player.name }}</span>
                  <span class="rp-position">{{ primaryPosition(player) }}</span>
                </div>
                <div class="rp-reason" v-if="isPlayerDisabled(player)">
                  {{ disabledReason(player) }}
                </div>
              </div>
            </div>
          </div>

          <!-- Historique des rolls -->
          <div v-if="rolledHistory.length > 0" class="roll-history">
            <div class="roll-history-title">Équipes rollées</div>
            <div v-for="(h, i) in rolledHistory" :key="i" class="roll-history-item">
              <span>{{ h.teamName }}</span>
              <span class="roll-history-season">{{ h.season }}</span>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useDraftStore } from '../stores/useDraftStore'
import { playerService, draftService } from '../services/api'
import FieldView from '../components/draft/FieldView.vue'
import { useLoadingStore } from '../stores/useLoadingStore'
import { storeToRefs } from 'pinia'

const router       = useRouter()
const draftStore   = useDraftStore()
const { isLoading: loading } = storeToRefs(useLoadingStore())

const draft             = ref({ name: 'Mon équipe' })
const rolling           = ref(false)
const rolledTeam        = ref(null)
const rolledPlayers     = ref([])
const rolledHistory     = ref([])
const hasPickedFromRoll = ref(false)

const takenPositions = computed(() =>
  (draftStore.players ?? []).map(p => p.primary_position_number)
)

const primaryPositionNumber = (player) =>
  player.positions?.find(p => p.pivot?.type === 'primary')?.number ?? null

const primaryPosition = (player) =>
  player.positions?.find(p => p.pivot?.type === 'primary')?.name ?? '—'

const isPlayerSelected = (player) =>
  (draftStore.players ?? []).some(p => p.id === player.id)

const isPlayerDisabled = (player) => {
  const pos = primaryPositionNumber(player)
  if (takenPositions.value.includes(pos)) return true
  if (isPlayerSelected(player)) return true
  if (hasPickedFromRoll.value) return true
  return false
}

const disabledReason = (player) => {
  if (isPlayerSelected(player)) return '✅ Choisi'
  const pos = primaryPositionNumber(player)
  if (takenPositions.value.includes(pos)) return 'Poste pris'
  return ''
}

const ratingClass = (rating) => {
  if (rating >= 85) return 'rating--gold'
  if (rating >= 70) return 'rating--silver'
  return 'rating--bronze'
}

const rollTeam = async () => {
  hasPickedFromRoll.value = false
  rolling.value = true
  try {
    const { data } = await playerService.roll({
      exclude: rolledHistory.value.map(h => ({ team_id: h.teamId, season_id: h.seasonId }))
    })
    rolledTeam.value    = { name: data.team_name, season: data.season_label }
    rolledPlayers.value = data.players
    rolledHistory.value.push({
      teamId:   data.team_id,
      seasonId: data.season_id,
      teamName: data.team_name,
      season:   data.season_label,
    })
  } catch (e) {
    console.error(e)
  } finally {
    rolling.value = false
  }
}

const selectPlayer = async (player) => {
  if (hasPickedFromRoll.value) return
  const positionNumber = primaryPositionNumber(player)
  if (!positionNumber) return

  hasPickedFromRoll.value = true

  if (!draft.value.id) {
    const { data } = await draftService.create({
      name:      draft.value.name,
      season_id: player.season_id,
    })
    draft.value = data
    if (data.session_token) {
      localStorage.setItem('session_token', data.session_token)
    }
  }

  await draftService.addPlayer(draft.value.id, {
    player_id:       player.id,
    position_number: positionNumber,
  })

  draftStore.addPlayer({
    ...player,
    position_number:         positionNumber,
    primary_position_number: positionNumber,
    season_label:            rolledTeam.value.season,
  })
}

const removePlayer = async (player) => {
  await draftService.removePlayer(draft.value.id, player.id)
  draftStore.removePlayer(player.id)
}

const goToSimulation = () => {
  router.push({ name: 'simulation', params: { draftId: draft.value.id } })
}
</script>

<style scoped>
.draft {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.draft-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.draft-header h1 { font-size: 1.8rem; font-weight: 700; }

.btn--primary {
  padding: 10px 24px;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  border: none;
  background-color: #2563eb;
  color: #ffffff;
  transition: all 0.2s;
}

.btn--primary:disabled { opacity: 0.4; cursor: not-allowed; }
.btn--primary:hover:not(:disabled) { background-color: #1d4ed8; }

.draft-content {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 24px;
  align-items: start;
}

.draft-left {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.draft-info {
  display: flex;
  justify-content: space-between;
  color: #94a3b8;
  font-size: 0.9rem;
}

.ready { color: #22c55e; font-weight: 600; }

/* Roll panel */
.roll-panel {
  background-color: #1a2634;
  border: 1px solid #1e3a5f;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  position: sticky;
  top: 80px;
  max-height: calc(100vh - 120px);
  overflow-y: auto;
}

.btn-roll {
  width: 100%;
  padding: 16px;
  border-radius: 10px;
  background: linear-gradient(135deg, #2563eb, #7c3aed);
  color: #ffffff;
  font-size: 1.2rem;
  font-weight: 700;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.btn-roll:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
}

.btn-roll:disabled { opacity: 0.4; cursor: not-allowed; }

.roll-spinning { animation: spin 0.8s linear infinite; display: inline-block; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Équipe rollée */
.rolled-team-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 10px;
  border-bottom: 1px solid #1e3a5f;
}

.rolled-team-name { font-weight: 700; font-size: 1rem; }
.rolled-team-season { font-size: 0.8rem; color: #64748b; }

.rolled-players {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.rolled-player {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 10px;
  border-radius: 8px;
  background-color: #0f1923;
  border: 1px solid #1e3a5f;
  cursor: pointer;
  transition: all 0.15s;
}

.rolled-player:hover:not(.rolled-player--disabled) {
  border-color: #2563eb;
  background-color: #1a2634;
}

.rolled-player--disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.rolled-player--selected {
  border-color: #22c55e;
  background-color: rgba(34, 197, 94, 0.1);
}

.rp-rating {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 0.8rem;
  flex-shrink: 0;
}

.rating--gold   { background-color: #78350f; color: #fbbf24; }
.rating--silver { background-color: #1e3a5f; color: #93c5fd; }
.rating--bronze { background-color: #1c1917; color: #a8a29e; }

.rp-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
}

.rp-name {
  font-size: 0.85rem;
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.rp-position { font-size: 0.72rem; color: #64748b; }

.rp-reason {
  font-size: 0.7rem;
  color: #ef4444;
  white-space: nowrap;
}

/* Historique */
.roll-history { border-top: 1px solid #1e3a5f; padding-top: 12px; }

.roll-history-title {
  font-size: 0.75rem;
  color: #475569;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

.roll-history-item {
  display: flex;
  justify-content: space-between;
  font-size: 0.8rem;
  color: #64748b;
  padding: 4px 0;
}

.roll-history-season { color: #475569; font-size: 0.75rem; }
</style>