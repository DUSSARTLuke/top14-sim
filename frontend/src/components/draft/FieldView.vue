<template>
  <div class="field-wrapper">
    <svg :viewBox="`0 0 380 620`" xmlns="http://www.w3.org/2000/svg" class="field-svg">
      <!-- Bandes alternées -->
      <rect x="0" y="0" width="380" height="620" fill="#1a6b2f" />
      <rect
        v-for="y in [0, 104, 208, 312, 416, 520]"
        :key="y"
        :x="0"
        :y="y"
        width="380"
        height="52"
        fill="#1e7535"
        opacity="0.5"
      />

      <!-- Zones de but -->
      <rect
        x="10"
        y="10"
        width="360"
        height="50"
        fill="rgba(255,255,255,0.04)"
        stroke="white"
        stroke-width="1.5"
      />
      <rect
        x="10"
        y="560"
        width="360"
        height="50"
        fill="rgba(255,255,255,0.04)"
        stroke="white"
        stroke-width="1.5"
      />

      <!-- Cadre -->
      <rect x="10" y="10" width="360" height="600" fill="none" stroke="white" stroke-width="2" />

      <!-- Lignes -->
      <line x1="10" y1="60" x2="370" y2="60" stroke="white" stroke-width="1.5" />
      <line
        x1="10"
        y1="165"
        x2="370"
        y2="165"
        stroke="white"
        stroke-width="1.5"
        stroke-dasharray="6,4"
      />
      <line
        x1="10"
        y1="255"
        x2="370"
        y2="255"
        stroke="white"
        stroke-width="1"
        stroke-dasharray="4,4"
        opacity="0.4"
      />
      <line x1="10" y1="310" x2="370" y2="310" stroke="white" stroke-width="2" />
      <line
        x1="10"
        y1="365"
        x2="370"
        y2="365"
        stroke="white"
        stroke-width="1"
        stroke-dasharray="4,4"
        opacity="0.4"
      />
      <line
        x1="10"
        y1="455"
        x2="370"
        y2="455"
        stroke="white"
        stroke-width="1.5"
        stroke-dasharray="6,4"
      />
      <line x1="10" y1="560" x2="370" y2="560" stroke="white" stroke-width="1.5" />

      <!-- Ligne centrale pointillée -->
      <line
        x1="190"
        y1="60"
        x2="190"
        y2="560"
        stroke="white"
        stroke-width="0.5"
        stroke-dasharray="3,6"
        opacity="0.3"
      />

      <!-- Points de drop -->
      <circle cx="190" cy="165" r="2" fill="white" opacity="0.5" />
      <circle cx="190" cy="455" r="2" fill="white" opacity="0.5" />

      <!-- Poteaux haut -->
      <line x1="155" y1="10" x2="155" y2="60" stroke="white" stroke-width="2.5" />
      <line x1="225" y1="10" x2="225" y2="60" stroke="white" stroke-width="2.5" />
      <line x1="155" y1="38" x2="225" y2="38" stroke="white" stroke-width="2.5" />
      <line x1="172" y1="55" x2="172" y2="65" stroke="white" stroke-width="2" />
      <line x1="208" y1="55" x2="208" y2="65" stroke="white" stroke-width="2" />

      <!-- Poteaux bas -->
      <line x1="155" y1="560" x2="155" y2="610" stroke="white" stroke-width="2.5" />
      <line x1="225" y1="560" x2="225" y2="610" stroke="white" stroke-width="2.5" />
      <line x1="155" y1="582" x2="225" y2="582" stroke="white" stroke-width="2.5" />
      <line x1="172" y1="555" x2="172" y2="565" stroke="white" stroke-width="2" />
      <line x1="208" y1="555" x2="208" y2="565" stroke="white" stroke-width="2" />

      <!-- Slots joueurs -->
      <g
        v-for="pos in positions"
        :key="pos.number"
        :transform="`translate(${pos.x}, ${pos.y})`"
        class="slot-group"
        :class="{
          'slot-compatible': !isOccupied(pos.number) && isCompatible(pos.number),
          'slot-incompatible':
            pendingPlayer && !isOccupied(pos.number) && !isCompatible(pos.number),
        }"
        @click="handleSlotClick(pos.number)"
        style="cursor: pointer"
      >
        <!-- Slot vide -->
        <template v-if="!getPlayer(pos.number)">
          <circle
            r="18"
            :fill="
              pendingPlayer && isCompatible(pos.number)
                ? 'rgba(201,168,76,0.25)'
                : 'rgba(0,0,0,0.3)'
            "
            :stroke="
              pendingPlayer && isCompatible(pos.number) ? '#C9A84C' : 'rgba(255,255,255,0.4)'
            "
            :stroke-width="pendingPlayer && isCompatible(pos.number) ? '2.5' : '1.5'"
            :stroke-dasharray="pendingPlayer && isCompatible(pos.number) ? 'none' : '3,2'"
          />
          <!-- Icône + si compatible -->
          <text
            text-anchor="middle"
            y="5"
            font-size="11"
            font-weight="700"
            font-family="sans-serif"
            :fill="pendingPlayer && isCompatible(pos.number) ? '#C9A84C' : 'rgba(255,255,255,0.6)'"
          >
            {{ pos.number }}
          </text>
          <text
            text-anchor="middle"
            y="30"
            font-size="7.5"
            font-family="sans-serif"
            :fill="pendingPlayer && isCompatible(pos.number) ? '#C9A84C' : 'rgba(255,255,255,0.45)'"
          >
            {{ pos.label }}
          </text>
        </template>

        <!-- Slot rempli -->
        <template v-else>
          <circle r="20" fill="#C9A84C" stroke="#C9A84C" stroke-width="2" class="slot-filled" />
          <text
            text-anchor="middle"
            y="-5"
            font-size="9"
            font-weight="700"
            font-family="sans-serif"
            fill="white"
          >
            {{ pos.number }}
          </text>
          <text
            text-anchor="middle"
            y="9"
            font-size="8"
            font-weight="600"
            font-family="sans-serif"
            fill="white"
          >
            {{ shortName(getPlayer(pos.number).name) }}
          </text>

          <!-- Tooltip -->
          <g class="tooltip" visibility="hidden">
            <rect
              x="-55"
              y="24"
              width="110"
              height="58"
              rx="4"
              fill="#0a0a0a"
              stroke="#C9A84C"
              stroke-width="1"
            />
            <text
              text-anchor="middle"
              y="37"
              font-size="7.5"
              font-family="sans-serif"
              fill="white"
              font-weight="600"
            >
              {{ getPlayer(pos.number).name }}
            </text>
            <text
              text-anchor="middle"
              y="49"
              font-size="7.5"
              font-family="sans-serif"
              fill="#93c5fd"
            >
              {{ getPlayer(pos.number).team?.name }}
            </text>
            <text
              text-anchor="middle"
              y="61"
              font-size="7.5"
              font-family="sans-serif"
              fill="#64748b"
            >
              {{ getPlayer(pos.number).season_label }}
            </text>
            <text
              text-anchor="middle"
              y="73"
              font-size="7.5"
              font-family="sans-serif"
              fill="#C9A84C"
            >
              Note : {{ getPlayer(pos.number).rating }}
            </text>
          </g>
        </template>
      </g>
    </svg>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  draftPlayers: { type: Array, default: () => [] },
  pendingPlayer: { type: Object, default: null },
})

const emit = defineEmits(['remove', 'slot-click'])

const positions = [
  { number: 1, label: 'Pilier G', x: 60, y: 100 },
  { number: 2, label: 'Talonneur', x: 120, y: 100 },
  { number: 3, label: 'Pilier D', x: 180, y: 100 },
  { number: 4, label: '2ème ligne', x: 90, y: 195 },
  { number: 5, label: '2ème ligne', x: 150, y: 195 },
  { number: 6, label: '3ème aile', x: 60, y: 265 },
  { number: 8, label: '3ème centre', x: 120, y: 265 },
  { number: 7, label: '3ème aile', x: 180, y: 265 },
  { number: 9, label: 'Demi mêlée', x: 130, y: 330 },
  { number: 10, label: 'Ouvreur', x: 200, y: 360 },
  { number: 11, label: 'Ailier G', x: 35, y: 480 },
  { number: 12, label: 'Centre', x: 145, y: 415 },
  { number: 13, label: 'Centre', x: 255, y: 420 },
  { number: 14, label: 'Ailier D', x: 345, y: 480 },
  { number: 15, label: 'Arrière', x: 190, y: 510 },
]

const getPlayer = (n) => props.draftPlayers.find((p) => p.position_number === n) || null

const shortName = (name) => {
  const parts = name.split(' ')
  if (parts.length === 1) return name
  return `${parts[0][0]}. ${parts.slice(1).join(' ')}`
}

const compatibleSlots = computed(() => {
  if (!props.pendingPlayer) return []
  return props.pendingPlayer.positions?.map((p) => p.number) ?? []
})

const isCompatible = (n) => compatibleSlots.value.includes(n)
const isOccupied = (n) => !!getPlayer(n)

const handleSlotClick = (n) => {
  if (isOccupied(n)) {
    emit('remove', getPlayer(n))
  } else if (props.pendingPlayer && isCompatible(n)) {
    emit('slot-click', n)
  }
}
</script>

<style scoped>
.field-wrapper {
  width: 100%;
  max-width: 420px;
  margin: 0 auto;
}

.field-svg {
  width: 100%;
  height: auto;
  border-radius: 8px;
  border: 2px solid #15803d;
}

.slot-filled {
  transition: fill 0.2s;
}

.slot-group:hover .slot-filled {
  fill: #dc2626;
}
.slot-group:hover .tooltip {
  visibility: visible;
}

.slot-compatible circle {
  filter: drop-shadow(0 0 6px rgba(201, 168, 76, 0.8));
}

.slot-incompatible {
  opacity: 0.3;
}

.slot-group:hover .tooltip {
  visibility: visible;
}
</style>
