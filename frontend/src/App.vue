<template>
  <div class="app">
    <AppHeader />
    <main class="main-content">
      <RouterView />
    </main>
    <LoadingOverlay :show="isLoading" />
  </div>
</template>

<script setup>
import AppHeader from './components/ui/AppHeader.vue'
import LoadingOverlay from './components/ui/LoadingOverlay.vue'
import { useLoadingStore } from './stores/useLoadingStore'
import { storeToRefs } from 'pinia'

const { isLoading } = storeToRefs(useLoadingStore())
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', sans-serif;
  background-color: #0f1923;
  color: #ffffff;
}

.app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
  padding: 24px;
  max-width: 1280px;
  margin: 0 auto;
  width: 100%;
}
</style>