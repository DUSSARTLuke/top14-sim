<template>
  <Transition name="fade">
    <div v-if="show" class="overlay">
      <div class="spinner"></div>
    </div>
  </Transition>
</template>

<script setup>
defineProps({ show: { type: Boolean, default: false } })
</script>

<style scoped>
.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
}

.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid rgba(255,255,255,0.1);
  border-top-color: #2563eb;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>