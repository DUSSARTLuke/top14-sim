<template>
  <div class="home">
    <!-- Hero -->
    <section class="hero">
      <div class="hero-badge">🏉 Top 14 Fantasy Simulation</div>
      <h1 class="hero-title">
        Crée ton équipe de légende.<br />
        <span class="hero-highlight">Simule le match.</span>
      </h1>
      <p class="hero-subtitle">
        Draft 15 joueurs issus de différentes saisons du Top 14,
        compose ton XV idéal et affronte d'autres équipes en simulation.
      </p>
      <div class="hero-actions">
        <RouterLink to="/draft" class="btn btn--primary">
          Créer une draft
        </RouterLink>
        <RouterLink v-if="!isLoggedIn" to="/login" class="btn btn--secondary">
          Se connecter
        </RouterLink>
        <RouterLink v-else to="/history" class="btn btn--secondary">
          Mes simulations
        </RouterLink>
      </div>
    </section>

    <!-- Comment ça marche -->
    <section class="how">
      <h2 class="section-title">Comment ça marche ?</h2>
      <div class="steps">
        <div class="step">
          <div class="step-number">1</div>
          <h3>Choisis une saison</h3>
          <p>Sélectionne une saison du Top 14 pour accéder aux joueurs disponibles.</p>
        </div>
        <div class="step">
          <div class="step-number">2</div>
          <h3>Drafte ton XV</h3>
          <p>Compose ton équipe de 15 joueurs en respectant les postes du rugby.</p>
        </div>
        <div class="step">
          <div class="step-number">3</div>
          <h3>Simule le match</h3>
          <p>Lance la simulation et regarde le match se dérouler action par action.</p>
        </div>
        <div class="step">
          <div class="step-number">4</div>
          <h3>Compare les résultats</h3>
          <p>Connecte-toi pour sauvegarder et comparer tes simulations.</p>
        </div>
      </div>
    </section>

    <!-- Stats rapides -->
    <section class="stats">
      <div class="stat-card">
        <span class="stat-number">14</span>
        <span class="stat-label">Clubs Top 14</span>
      </div>
      <div class="stat-card">
        <span class="stat-number">7+</span>
        <span class="stat-label">Saisons disponibles</span>
      </div>
      <div class="stat-card">
        <span class="stat-number">15</span>
        <span class="stat-label">Postes par équipe</span>
      </div>
      <div class="stat-card">
        <span class="stat-number">∞</span>
        <span class="stat-label">Combinaisons possibles</span>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const isLoggedIn = computed(() => !!localStorage.getItem('auth_token'))
</script>

<style scoped>
.home {
  display: flex;
  flex-direction: column;
  gap: 80px;
  padding: 40px 0;
}

/* Hero */
.hero {
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 24px;
}

.hero-badge {
  background-color: #1e3a5f;
  color: #60a5fa;
  padding: 6px 16px;
  border-radius: 999px;
  font-size: 0.85rem;
  font-weight: 500;
  border: 1px solid #2563eb;
}

.hero-title {
  font-size: 3rem;
  font-weight: 800;
  line-height: 1.2;
  max-width: 700px;
}

.hero-highlight {
  color: #2563eb;
}

.hero-subtitle {
  color: #94a3b8;
  font-size: 1.1rem;
  max-width: 560px;
  line-height: 1.7;
}

.hero-actions {
  display: flex;
  gap: 16px;
  margin-top: 8px;
}

.btn {
  padding: 12px 28px;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
}

.btn--primary {
  background-color: #2563eb;
  color: #ffffff;
}

.btn--primary:hover {
  background-color: #1d4ed8;
  transform: translateY(-1px);
}

.btn--secondary {
  background-color: transparent;
  color: #ffffff;
  border: 1px solid #334155;
}

.btn--secondary:hover {
  border-color: #ffffff;
  transform: translateY(-1px);
}

/* How it works */
.section-title {
  font-size: 1.8rem;
  font-weight: 700;
  text-align: center;
  margin-bottom: 40px;
}

.steps {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

.step {
  background-color: #1a2634;
  border: 1px solid #1e3a5f;
  border-radius: 12px;
  padding: 28px 24px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.step-number {
  width: 40px;
  height: 40px;
  background-color: #2563eb;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.1rem;
}

.step h3 {
  font-size: 1rem;
  font-weight: 600;
}

.step p {
  color: #94a3b8;
  font-size: 0.9rem;
  line-height: 1.6;
}

/* Stats */
.stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.stat-card {
  background-color: #1a2634;
  border: 1px solid #1e3a5f;
  border-radius: 12px;
  padding: 32px 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.stat-number {
  font-size: 2.5rem;
  font-weight: 800;
  color: #2563eb;
}

.stat-label {
  color: #94a3b8;
  font-size: 0.9rem;
}
</style>