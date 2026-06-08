<template>
  <div class="field-wrapper">
    <svg :viewBox="`0 0 380 620`" xmlns="http://www.w3.org/2000/svg" class="field-svg">

      <!-- Bandes alternées -->
      <rect x="0" y="0" width="380" height="620" fill="#1a6b2f"/>
      <rect v-for="y in [0,104,208,312,416,520]" :key="y" :x="0" :y="y" width="380" height="52" fill="#1e7535" opacity="0.5"/>

      <!-- Zones de but -->
      <rect x="10" y="10"  width="360" height="50" fill="rgba(255,255,255,0.04)" stroke="white" stroke-width="1.5"/>
      <rect x="10" y="560" width="360" height="50" fill="rgba(255,255,255,0.04)" stroke="white" stroke-width="1.5"/>

      <!-- Cadre -->
      <rect x="10" y="10" width="360" height="600" fill="none" stroke="white" stroke-width="2"/>

      <!-- Lignes -->
      <line x1="10" y1="60"  x2="370" y2="60"  stroke="white" stroke-width="1.5"/>
      <line x1="10" y1="165" x2="370" y2="165" stroke="white" stroke-width="1.5" stroke-dasharray="6,4"/>
      <line x1="10" y1="255" x2="370" y2="255" stroke="white" stroke-width="1"   stroke-dasharray="4,4" opacity="0.7"/>
      <line x1="10" y1="310" x2="370" y2="310" stroke="white" stroke-width="2"/>
      <line x1="10" y1="365" x2="370" y2="365" stroke="white" stroke-width="1"   stroke-dasharray="4,4" opacity="0.7"/>
      <line x1="10" y1="455" x2="370" y2="455" stroke="white" stroke-width="1.5" stroke-dasharray="6,4"/>
      <line x1="10" y1="560" x2="370" y2="560" stroke="white" stroke-width="1.5"/>

      <!-- Ligne centrale pointillée -->
      <line x1="190" y1="60" x2="190" y2="560" stroke="white" stroke-width="0.5" stroke-dasharray="3,6" opacity="0.3"/>

      <!-- Cercle central -->
      <circle cx="190" cy="310" r="30" fill="none" stroke="white" stroke-width="1" opacity="0.5"/>
      <circle cx="190" cy="310" r="2" fill="white" opacity="0.6"/>

      <!-- Points de drop -->
      <circle cx="190" cy="165" r="2" fill="white" opacity="0.5"/>
      <circle cx="190" cy="455" r="2" fill="white" opacity="0.5"/>

      <!-- Poteaux haut -->
      <line x1="155" y1="10" x2="155" y2="60" stroke="white" stroke-width="2.5"/>
      <line x1="225" y1="10" x2="225" y2="60" stroke="white" stroke-width="2.5"/>
      <line x1="155" y1="38" x2="225" y2="38" stroke="white" stroke-width="2.5"/>
      <line x1="172" y1="55" x2="172" y2="65" stroke="white" stroke-width="2"/>
      <line x1="208" y1="55" x2="208" y2="65" stroke="white" stroke-width="2"/>

      <!-- Poteaux bas -->
      <line x1="155" y1="560" x2="155" y2="610" stroke="white" stroke-width="2.5"/>
      <line x1="225" y1="560" x2="225" y2="610" stroke="white" stroke-width="2.5"/>
      <line x1="155" y1="582" x2="225" y2="582" stroke="white" stroke-width="2.5"/>
      <line x1="172" y1="555" x2="172" y2="565" stroke="white" stroke-width="2"/>
      <line x1="208" y1="555" x2="208" y2="565" stroke="white" stroke-width="2"/>

      <!-- Labels lignes -->
      <text x="15" y="58"  font-size="8" font-family="sans-serif" fill="rgba(255,255,255,0.45)">Ligne de but</text>
      <text x="15" y="163" font-size="8" font-family="sans-serif" fill="rgba(255,255,255,0.45)">22m</text>
      <text x="15" y="308" font-size="8" font-family="sans-serif" fill="rgba(255,255,255,0.55)">Médiane</text>
      <text x="15" y="453" font-size="8" font-family="sans-serif" fill="rgba(255,255,255,0.45)">22m</text>
      <text x="15" y="558" font-size="8" font-family="sans-serif" fill="rgba(255,255,255,0.45)">Ligne de but</text>

      <!-- Slots joueurs -->
      <g v-for="pos in positions" :key="pos.number"
        :transform="`translate(${pos.x}, ${pos.y})`"
        class="slot-group"
        @click="getPlayer(pos.number) && $emit('remove', getPlayer(pos.number))"
        style="cursor: pointer"
      >
        <!-- Slot vide -->
        <template v-if="!getPlayer(pos.number)">
          <circle r="18" fill="rgba(0,0,0,0.3)" stroke="rgba(255,255,255,0.4)" stroke-width="1.5" stroke-dasharray="3,2"/>
          <text text-anchor="middle" y="5" font-size="11" font-weight="700" font-family="sans-serif" fill="rgba(255,255,255,0.6)">{{ pos.number }}</text>
          <text text-anchor="middle" y="30" font-size="7.5" font-family="sans-serif" fill="rgba(255,255,255,0.45)">{{ pos.label }}</text>
        </template>

        <!-- Slot rempli -->
<template v-else>
  <circle r="20" fill="#2563eb" stroke="#93c5fd" stroke-width="2" class="slot-filled"/>
  <text text-anchor="middle" y="-5" font-size="9" font-weight="700" font-family="sans-serif" fill="white">{{ pos.number }}</text>
  <text text-anchor="middle" y="9" font-size="8" font-weight="600" font-family="sans-serif" fill="white">{{ shortName(getPlayer(pos.number).name) }}</text>

  <!-- Tooltip -->
  <g class="tooltip" visibility="hidden">
    <rect x="-55" y="24" width="110" height="52" rx="4" fill="#0f1923" stroke="#2563eb" stroke-width="1"/>
    <text text-anchor="middle" y="37" font-size="7.5" font-family="sans-serif" fill="white" font-weight="600">{{ getPlayer(pos.number).name }}</text>
    <text text-anchor="middle" y="49" font-size="7.5" font-family="sans-serif" fill="#93c5fd">{{ getPlayer(pos.number).team?.name }}</text>
    <text text-anchor="middle" y="61" font-size="7.5" font-family="sans-serif" fill="#64748b">{{ getPlayer(pos.number).season_label ?? getPlayer(pos.number).season?.label }}</text>
    <text text-anchor="middle" y="73" font-size="7.5" font-family="sans-serif" fill="#fbbf24">Note : {{ getPlayer(pos.number).rating }}</text>
  </g>
</template>
      </g>
    </svg>
  </div>
</template>

<script setup>
const props = defineProps({
  draftPlayers: { type: Array, default: () => [] }
})

defineEmits(['remove'])

const positions = [
  { number: 1,  label: 'Pilier G',     x: 80,  y: 100 },
  { number: 2,  label: 'Talonneur',    x: 190, y: 100 },
  { number: 3,  label: 'Pilier D',     x: 300, y: 100 },
  { number: 4,  label: '2ème ligne',   x: 130, y: 195 },
  { number: 5,  label: '2ème ligne',   x: 250, y: 195 },
  { number: 6,  label: '3ème aile',    x: 60,  y: 265 },
  { number: 8,  label: '3ème centre',  x: 190, y: 265 },
  { number: 7,  label: '3ème aile',    x: 320, y: 265 },
  { number: 9,  label: 'Demi mêlée',   x: 130, y: 355 },
  { number: 10, label: 'Ouvreur',      x: 260, y: 355 },
  { number: 11, label: 'Ailier G',     x: 35,  y: 430 },
  { number: 12, label: 'Centre',       x: 145, y: 415 },
  { number: 13, label: 'Centre',       x: 255, y: 430 },
  { number: 14, label: 'Ailier D',     x: 345, y: 445 },
  { number: 15, label: 'Arrière',      x: 190, y: 510 },
]

const getPlayer = (n) => props.draftPlayers.find(p => p.position_number === n) || null

const shortName = (name) => {
  const parts = name.split(' ')
  if (parts.length === 1) return name
  return `${parts[0][0]}. ${parts.slice(1).join(' ')}`
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
</style>