<template>
  <div class="draft">
    <!-- Header -->
    <div class="draft-header">
      <div class="draft-stats">
        <div class="stat-block">
          <span class="stat-val">{{ avgRating }}</span>
          <span class="stat-lbl">MOYENNE</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-block">
          <div class="stat-dots">
            <span
              v-for="i in 15"
              :key="i"
              class="stat-dot"
              :class="{ 'stat-dot--filled': i <= (draftStore.players?.length ?? 0) }"
            ></span>
          </div>
          <span class="stat-lbl">TIRAGE {{ draftStore.players?.length ?? 0 }}/15</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-block">
          <span class="stat-val">{{ draftStore.players?.length ?? 0 }}</span>
          <span class="stat-lbl">RECRUTÉS</span>
        </div>
      </div>
      <button
        class="btn-simulate"
        :disabled="(draftStore.players?.length ?? 0) < 15"
        @click="goToSimulation"
      >
        ⚡ Simuler le match
      </button>
    </div>

    <div class="draft-content">
      <!-- Terrain -->
      <div class="draft-left">
        <FieldView
          :draft-players="draftStore.players ?? []"
          :pending-player="pendingPlayer"
          @remove="removePlayer"
          @slot-click="confirmPosition"
        />
      </div>

      <!-- Panel Roll -->
      <div class="draft-right">
        <div class="roll-panel">
          <!-- Roulette -->
          <div class="roulette-section">
            <div class="roulette-title">
              <svg width="16" height="10" viewBox="0 0 100 60" fill="none">
                <ellipse
                  cx="50"
                  cy="30"
                  rx="48"
                  ry="28"
                  stroke="#C9A84C"
                  stroke-width="4"
                  fill="none"
                />
                <line x1="2" y1="30" x2="98" y2="30" stroke="#C9A84C" stroke-width="3" />
                <line x1="50" y1="4" x2="50" y2="56" stroke="#C9A84C" stroke-width="2" />
              </svg>
              ROULETTE
            </div>

            <!-- Club + Saison tirés -->
            <div class="slot-machine">
              <div class="slot-machine-reel">
                <div
                  class="slot-reel-track"
                  :style="{ transform: `translateY(-${clubOffset}px)`, transition: clubTransition }"
                >
                  <div v-for="(item, i) in clubReel" :key="i" class="slot-reel-item">
                    {{ item }}
                  </div>
                </div>
              </div>
              <div class="slot-machine-sep">·</div>
              <div class="slot-machine-reel">
                <div
                  class="slot-reel-track"
                  :style="{
                    transform: `translateY(-${seasonOffset}px)`,
                    transition: seasonTransition,
                  }"
                >
                  <div v-for="(item, i) in seasonReel" :key="i" class="slot-reel-item">
                    {{ item }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Bouton TIRER -->
            <button
              class="btn-roll"
              :disabled="
                rolling || loading || mustPickBeforeRoll || (draftStore.players?.length ?? 0) >= 15
              "
              @click="rollTeam"
            >
              <svg
                :class="{ 'roll-spinning': rolling }"
                width="20"
                height="12"
                viewBox="0 0 100 60"
                fill="none"
              >
                <ellipse
                  cx="50"
                  cy="30"
                  rx="48"
                  ry="28"
                  stroke="white"
                  stroke-width="4"
                  fill="none"
                />
                <line x1="2" y1="30" x2="98" y2="30" stroke="white" stroke-width="3" />
                <line x1="50" y1="4" x2="50" y2="56" stroke="white" stroke-width="2" />
                <line x1="30" y1="8" x2="30" y2="52" stroke="white" stroke-width="1.5" />
                <line x1="70" y1="8" x2="70" y2="52" stroke="white" stroke-width="1.5" />
              </svg>
              {{ rolling ? 'TIRAGE...' : rolledTeam ? '✓ TIRÉ' : 'TIRER' }}
            </button>

            <div v-if="mustPickBeforeRoll" class="roll-hint">
              👆 Choisis un joueur avant de re-tirer
            </div>

            <!-- Skips -->
            <div class="skip-row">
              <button
                class="skip-btn"
                :disabled="skipClubLeft <= 0 || rolling || mustPickBeforeRoll"
                @click="skipClub"
              >
                ↺ Club ({{ skipClubLeft }})
              </button>
              <button
                class="skip-btn"
                :disabled="skipSeasonLeft <= 0 || rolling || mustPickBeforeRoll"
                @click="skipSeason"
              >
                ↺ Saison ({{ skipSeasonLeft }})
              </button>
            </div>
          </div>

          <!-- Liste joueurs rollés -->
          <div v-if="rolledTeam" class="players-section">
            <div class="players-section-header">
              <span class="players-section-title"
                >{{ rolledTeam.name }} · {{ rolledTeam.season }}</span
              >
              <span class="players-count">{{ rolledPlayers.length }} joueurs</span>
            </div>

            <p class="players-hint">Clique sur un joueur pour voir ses postes compatibles</p>

            <!-- Grille joueurs -->
            <div
              class="players-grid"
              :class="{ 'players-grid--locked': hasPickedFromRoll || savingPosition }"
            >
              <div
                v-for="player in rolledPlayers"
                :key="player.id"
                class="player-card"
                :class="{
                  'player-card--disabled':
                    isPlayerDisabled(player) || (hasPickedFromRoll && !isPlayerSelected(player)),
                  'player-card--selected': isPlayerSelected(player),
                  'player-card--pending': pendingPlayer?.id === player.id,
                }"
                @click="!isPlayerDisabled(player) && !hasPickedFromRoll && selectPlayer(player)"
              >
                <!-- Postes compatibles -->
                <div class="pc-positions">
                  <span
                    v-for="pos in player.positions"
                    :key="pos.id"
                    class="pc-pos"
                    :class="{ 'pc-pos--secondary': pos.pivot?.type === 'secondary' }"
                    >{{ pos.number }}</span
                  >
                </div>

                <!-- Nom -->
                <div class="pc-name">{{ player.name }}</div>

                <!-- Infos -->
                <div class="pc-meta">
                  <span class="pc-flag">🏉</span>
                  <span class="pc-pos-name">{{ primaryPosition(player) }}</span>
                </div>

                <!-- Barre + note -->
                <div class="pc-rating-row">
                  <div class="pc-bar">
                    <div
                      class="pc-bar-fill"
                      :style="{ width: player.rating + '%' }"
                      :class="ratingClass(player.rating)"
                    ></div>
                  </div>
                  <span class="pc-rating" :class="ratingClass(player.rating)">{{
                    player.rating
                  }}</span>
                </div>

                <!-- Raison disabled -->
                <div class="pc-reason" v-if="isPlayerDisabled(player)">
                  {{ disabledReason(player) }}
                </div>
                <div class="pc-check" v-if="isPlayerSelected(player)">✓</div>
              </div>
            </div>
          </div>

          <!-- Historique -->
          <div v-if="rolledHistory.length > 0" class="roll-history">
            <div class="roll-history-title">Équipes tirées</div>
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
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useDraftStore } from '../stores/useDraftStore'
import { playerService, draftService } from '../services/api'
import FieldView from '../components/draft/FieldView.vue'
import { useLoadingStore } from '../stores/useLoadingStore'
import { storeToRefs } from 'pinia'

const router = useRouter()
const draftStore = useDraftStore()
const { isLoading: loading } = storeToRefs(useLoadingStore())

const draft = ref({ name: 'Mon équipe' })
const rolling = ref(false)
const rolledTeam = ref(null)
const rolledPlayers = ref([])
const rolledHistory = ref([])
const hasPickedFromRoll = ref(false)
const pendingPlayer = ref(null)
const savingPosition = ref(false)
const skipClubLeft = ref(1)
const skipSeasonLeft = ref(1)
// Machine à sous
const clubReel = ref(['—'])
const seasonReel = ref(['—'])
const clubOffset = ref(0)
const seasonOffset = ref(0)
const clubTransition = ref('none')
const seasonTransition = ref('none')
const ITEM_HEIGHT = 48

const takenPositions = computed(() =>
  (draftStore.players ?? []).map((p) => p.primary_position_number),
)

const avgRating = computed(() => {
  const players = draftStore.players ?? []
  if (!players.length) return '—'
  return Math.round(players.reduce((acc, p) => acc + (p.rating ?? 0), 0) / players.length)
})

const mustPickBeforeRoll = computed(() => rolledTeam.value !== null && !hasPickedFromRoll.value)

const availablePositions = computed(() => {
  if (!pendingPlayer.value) return []
  return (
    pendingPlayer.value.positions?.filter((pos) => !takenPositions.value.includes(pos.number)) ?? []
  )
})

const primaryPositionNumber = (player) =>
  player.positions?.find((p) => p.pivot?.type === 'primary')?.number ?? null

const primaryPosition = (player) =>
  player.positions?.find((p) => p.pivot?.type === 'primary')?.name ?? '—'

const isPlayerSelected = (player) => (draftStore.players ?? []).some((p) => p.id === player.id)

const isPlayerDisabled = (player) => {
  const positions = player.positions ?? []
  const allPosTaken = positions.every((pos) => takenPositions.value.includes(pos.number))
  if (allPosTaken) return true
  if (isPlayerSelected(player)) return true
  if (hasPickedFromRoll.value) return true
  return false
}

const disabledReason = (player) => {
  if (isPlayerSelected(player)) return '✓ Recruté'
  const positions = player.positions ?? []
  const allPosTaken = positions.every((pos) => takenPositions.value.includes(pos.number))
  if (allPosTaken) return 'Poste(s) pris'
  return ''
}

const ratingClass = (rating) => {
  if (rating >= 85) return 'rating--gold'
  if (rating >= 70) return 'rating--silver'
  return 'rating--bronze'
}

const allClubs = ref([])
const allSeasons = ref([])

// Charge les noms dispo au montage
onMounted(async () => {
  const { data: seasons } = await import('../services/api').then((m) => m.seasonService.getAll())
  allSeasons.value = seasons.map((s) => s.label)
})

const rollTeam = async () => {
  hasPickedFromRoll.value = false
  rolling.value = true

  const fakeClubs = [
    'Toulouse',
    'La Rochelle',
    'Racing 92',
    'Clermont',
    'Bordeaux',
    'Toulon',
    'Lyon',
    'Montpellier',
    'Stade Français',
    'Castres',
  ]
  const fakeSeasons = ['2019-20', '2020-21', '2021-22', '2022-23', '2023-24', '2024-25']

  // Animation infinie immédiate
  let spinning = true
  clubReel.value = [...fakeClubs]
  seasonReel.value = [...fakeSeasons]
  clubOffset.value = 0
  seasonOffset.value = 0
  clubTransition.value = 'none'
  seasonTransition.value = 'none'

  await new Promise((r) => setTimeout(r, 50))
  clubTransition.value = 'transform 1.5s linear infinite'
  seasonTransition.value = 'transform 1.5s linear infinite'
  clubOffset.value = fakeClubs.length * ITEM_HEIGHT
  seasonOffset.value = fakeSeasons.length * ITEM_HEIGHT

  try {
    const { data } = await playerService.roll({
      exclude: rolledHistory.value.map((h) => ({ team_id: h.teamId, season_id: h.seasonId })),
    })

    // Arrêter sur le bon résultat
    await animateReel(clubReel, clubOffset, clubTransition, fakeClubs, data.team_name)
    setTimeout(
      () => animateReel(seasonReel, seasonOffset, seasonTransition, fakeSeasons, data.season_label),
      200,
    )

    setTimeout(() => {
      rolledTeam.value = { name: data.team_name, season: data.season_label }
      rolledPlayers.value = data.players
      rolledHistory.value.push({
        teamId: data.team_id,
        seasonId: data.season_id,
        teamName: data.team_name,
        season: data.season_label,
      })
    }, 1000)
  } catch (e) {
    console.error(e)
  } finally {
    setTimeout(() => {
      rolling.value = false
    }, 2000)
  }
}

const skipClub = async () => {
  if (skipClubLeft.value <= 0) return
  skipClubLeft.value--
  hasPickedFromRoll.value = false
  rolling.value = true
  try {
    const { data } = await playerService.roll({
      exclude: rolledHistory.value.map((h) => ({ team_id: h.teamId, season_id: h.seasonId })),
      keep_season: rolledTeam.value?.season,
    })
    rolledTeam.value = { name: data.team_name, season: data.season_label }
    rolledPlayers.value = data.players
    rolledHistory.value.push({
      teamId: data.team_id,
      seasonId: data.season_id,
      teamName: data.team_name,
      season: data.season_label,
    })
  } catch (e) {
    console.error(e)
  } finally {
    rolling.value = false
  }
}

const skipSeason = async () => {
  if (skipSeasonLeft.value <= 0) return
  skipSeasonLeft.value--
  hasPickedFromRoll.value = false
  rolling.value = true
  try {
    const { data } = await playerService.roll({
      exclude: rolledHistory.value.map((h) => ({ team_id: h.teamId, season_id: h.seasonId })),
      keep_club: rolledTeam.value?.name,
    })
    rolledTeam.value = { name: data.team_name, season: data.season_label }
    rolledPlayers.value = data.players
    rolledHistory.value.push({
      teamId: data.team_id,
      seasonId: data.season_id,
      teamName: data.team_name,
      season: data.season_label,
    })
  } catch (e) {
    console.error(e)
  } finally {
    rolling.value = false
  }
}

const selectPlayer = (player) => {
  if (hasPickedFromRoll.value) return
  // Si on reclique sur le même joueur, on désélectionne
  if (pendingPlayer.value?.id === player.id) {
    pendingPlayer.value = null
    return
  }
  pendingPlayer.value = player
}

const cancelPending = () => {
  pendingPlayer.value = null
}

const confirmPosition = async (positionNumber) => {
  if (savingPosition.value) return
  savingPosition.value = true
  hasPickedFromRoll.value = true

  const player = pendingPlayer.value

  if (!draft.value.id) {
    const { data } = await draftService.create({
      name: draft.value.name,
      season_id: player.season_id,
    })
    draft.value = data
    if (data.session_token) {
      localStorage.setItem('session_token', data.session_token)
    }
  }

  await draftService.addPlayer(draft.value.id, {
    player_id: player.id,
    position_number: positionNumber,
  })

  draftStore.addPlayer({
    ...player,
    position_number: positionNumber,
    primary_position_number: positionNumber,
    season_label: rolledTeam.value.season,
  })

  pendingPlayer.value = null
  savingPosition.value = false
}

const removePlayer = async (player) => {
  await draftService.removePlayer(draft.value.id, player.id)
  draftStore.removePlayer(player.id)
}

const goToSimulation = () => {
  router.push({ name: 'simulation', params: { draftId: draft.value.id } })
}

const animateReel = async (reelRef, offsetRef, transitionRef, items, finalValue) => {
  // Construire la liste : items aléatoires + valeur finale
  const fakeItems = []
  for (let i = 0; i < 12; i++) {
    fakeItems.push(items[Math.floor(Math.random() * items.length)])
  }
  fakeItems.push(finalValue)

  reelRef.value = fakeItems
  offsetRef.value = 0
  transitionRef.value = 'none'

  await new Promise((r) => setTimeout(r, 50))

  // Animer vers le bas avec décélération
  transitionRef.value = 'transform 1.8s cubic-bezier(0.25, 0.1, 0.1, 1)'
  offsetRef.value = (fakeItems.length - 1) * ITEM_HEIGHT
}
</script>

<style scoped>
.draft {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Header stats */
.draft-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #111111;
  border: 1px solid #222;
  border-radius: 12px;
  padding: 16px 24px;
}

.draft-stats {
  display: flex;
  align-items: center;
  gap: 24px;
}

.stat-block {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.stat-val {
  font-size: 1.6rem;
  font-weight: 900;
  color: #c9a84c;
  line-height: 1;
}

.stat-lbl {
  font-size: 0.65rem;
  color: #475569;
  letter-spacing: 1px;
  text-transform: uppercase;
}

.stat-divider {
  width: 1px;
  height: 36px;
  background: #222;
}

.stat-dots {
  display: flex;
  gap: 3px;
  flex-wrap: wrap;
  max-width: 120px;
}

.stat-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #222;
  border: 1px solid #333;
}

.stat-dot--filled {
  background: #c9a84c;
  border-color: #c9a84c;
}

.btn-simulate {
  padding: 10px 24px;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 700;
  cursor: pointer;
  border: 1px solid #c9a84c;
  background: transparent;
  color: #c9a84c;
  transition: all 0.2s;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn-simulate:hover:not(:disabled) {
  background: #c9a84c;
  color: #000000;
}

.btn-simulate:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

/* Layout */
.draft-content {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 24px;
  align-items: start;
}

.draft-left {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

/* Roll panel */
.roll-panel {
  background: #111111;
  border: 1px solid #222;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  position: sticky;
  top: 80px;
  max-height: calc(100vh - 120px);
  overflow-y: auto;
}

.roll-panel::-webkit-scrollbar {
  width: 4px;
}
.roll-panel::-webkit-scrollbar-thumb {
  background: #333;
  border-radius: 4px;
}

/* Roulette section */
.roulette-section {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.roulette-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.75rem;
  font-weight: 700;
  color: #c9a84c;
  letter-spacing: 2px;
  text-transform: uppercase;
}

.roulette-cards {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}

.roulette-card {
  background: #0a0a0a;
  border: 1px solid #222;
  border-radius: 8px;
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.roulette-card-val {
  font-size: 0.95rem;
  font-weight: 700;
  color: #ffffff;
}

.roulette-card-lbl {
  font-size: 0.65rem;
  color: #475569;
  letter-spacing: 1px;
  text-transform: uppercase;
}

.btn-roll {
  width: 100%;
  padding: 14px;
  border-radius: 8px;
  background: linear-gradient(135deg, #c9a84c, #3a2a00);
  color: #ffffff;
  font-size: 1rem;
  font-weight: 800;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  letter-spacing: 1px;
}

.btn-roll:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(201, 168, 76, 0.3);
}

.btn-roll:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.roll-spinning {
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.roll-hint {
  text-align: center;
  font-size: 0.78rem;
  color: #c9a84c;
  opacity: 0.8;
}

/* Skips */
.skip-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}

.skip-btn {
  padding: 8px;
  border-radius: 6px;
  background: transparent;
  border: 1px solid #222;
  color: #64748b;
  font-size: 0.78rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.skip-btn:hover:not(:disabled) {
  border-color: #c9a84c;
  color: #c9a84c;
}

.skip-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

/* Players section */
.players-section {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.players-section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.players-section-title {
  font-size: 0.85rem;
  font-weight: 700;
  color: #ffffff;
}

.players-count {
  font-size: 0.75rem;
  color: #475569;
}

.players-hint {
  font-size: 0.75rem;
  color: #475569;
  text-align: center;
  padding: 8px;
  background: #0a0a0a;
  border-radius: 6px;
  border: 1px solid #1a1a1a;
}

/* Position picker */
.position-picker {
  background: #0a0a0a;
  border: 1px solid #c9a84c;
  border-radius: 10px;
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.position-picker-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: 700;
  font-size: 0.9rem;
  color: #ffffff;
}

.close-btn {
  background: none;
  border: none;
  color: #64748b;
  cursor: pointer;
  font-size: 1rem;
  padding: 0;
}

.close-btn:hover {
  color: #ffffff;
}

.position-list {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.position-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  background: #111111;
  border: 1px solid #222;
  border-radius: 8px;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.15s;
}

.position-btn:hover:not(:disabled) {
  border-color: #c9a84c;
  background: #1a1500;
}

.position-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.pos-number {
  width: 26px;
  height: 26px;
  background: #c9a84c;
  color: #000000;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 0.8rem;
  flex-shrink: 0;
}

.pos-name {
  flex: 1;
  font-size: 0.85rem;
}
.pos-type {
  font-size: 0.7rem;
  color: #64748b;
  font-style: italic;
}
.no-positions {
  color: #475569;
  font-size: 0.82rem;
  text-align: center;
  padding: 10px 0;
}

/* Players grid */
.players-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}

.players-grid--locked .player-card:not(.player-card--selected):not(.player-card--pending) {
  opacity: 0.35;
  pointer-events: none;
}

.player-card {
  background: #0a0a0a;
  border: 1px solid #1a1a1a;
  border-radius: 8px;
  padding: 10px;
  cursor: pointer;
  transition: all 0.15s;
  display: flex;
  flex-direction: column;
  gap: 5px;
  position: relative;
}

.player-card:hover:not(.player-card--disabled) {
  border-color: #c9a84c;
  background: #0f0d00;
}

.player-card--disabled {
  cursor: not-allowed;
}

.player-card--selected {
  border-color: #22c55e;
  background: rgba(34, 197, 94, 0.08);
}

.player-card--pending {
  border-color: #c9a84c;
  background: rgba(201, 168, 76, 0.12);
  box-shadow: 0 0 12px rgba(201, 168, 76, 0.3);
}

.pc-positions {
  display: flex;
  gap: 3px;
  flex-wrap: wrap;
}

.pc-pos {
  background: #1a1a1a;
  color: #c9a84c;
  font-size: 0.65rem;
  font-weight: 700;
  padding: 1px 5px;
  border-radius: 3px;
}

.pc-pos--secondary {
  color: #64748b;
  background: #111;
}

.pc-name {
  font-size: 0.82rem;
  font-weight: 700;
  color: #ffffff;
  line-height: 1.2;
}

.pc-meta {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.7rem;
  color: #64748b;
}

.pc-rating-row {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 2px;
}

.pc-bar {
  flex: 1;
  height: 3px;
  background: #1a1a1a;
  border-radius: 2px;
  overflow: hidden;
}

.pc-bar-fill {
  height: 100%;
  border-radius: 2px;
  transition: width 0.3s;
}

.rating--gold .pc-bar-fill,
.pc-bar-fill.rating--gold {
  background: #c9a84c;
}
.rating--silver .pc-bar-fill,
.pc-bar-fill.rating--silver {
  background: #93c5fd;
}
.rating--bronze .pc-bar-fill,
.pc-bar-fill.rating--bronze {
  background: #a8a29e;
}

.pc-rating {
  font-size: 0.85rem;
  font-weight: 800;
  min-width: 24px;
  text-align: right;
}

.rating--gold {
  color: #c9a84c;
}
.rating--silver {
  color: #93c5fd;
}
.rating--bronze {
  color: #a8a29e;
}

.pc-reason {
  font-size: 0.65rem;
  color: #ef4444;
}

.pc-check {
  position: absolute;
  top: 6px;
  right: 8px;
  color: #22c55e;
  font-weight: 700;
  font-size: 0.8rem;
}

/* Historique */
.roll-history {
  border-top: 1px solid #1a1a1a;
  padding-top: 12px;
}

.roll-history-title {
  font-size: 0.7rem;
  color: #333;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 8px;
}

.roll-history-item {
  display: flex;
  justify-content: space-between;
  font-size: 0.78rem;
  color: #333;
  padding: 3px 0;
}

.roll-history-season {
  font-size: 0.72rem;
}

/* Machine à sous */
.slot-machine {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #0a0a0a;
  border: 1px solid #333;
  border-radius: 10px;
  padding: 4px 8px;
  height: 56px;
  overflow: hidden;
  position: relative;
}

/* Dégradé haut/bas pour l'effet de disparition */
.slot-machine::before,
.slot-machine::after {
  content: '';
  position: absolute;
  left: 0;
  right: 0;
  height: 16px;
  z-index: 2;
  pointer-events: none;
}

.slot-machine::before {
  top: 0;
  background: linear-gradient(to bottom, #0a0a0a, transparent);
}

.slot-machine::after {
  bottom: 0;
  background: linear-gradient(to top, #0a0a0a, transparent);
}

.slot-machine-reel {
  flex: 1;
  height: 48px;
  overflow: hidden;
  position: relative;
}

.slot-reel-track {
  display: flex;
  flex-direction: column;
}

.slot-reel-item {
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
  font-weight: 700;
  color: #c9a84c;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  padding: 0 4px;
  text-align: center;
}

.slot-machine-sep {
  color: #333;
  font-size: 1.2rem;
  flex-shrink: 0;
}
</style>
