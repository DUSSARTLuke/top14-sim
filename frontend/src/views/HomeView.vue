<template>
  <div class="home">

    <!-- Hero -->
    <section class="hero">
      <div class="hero-badge">🏉 TOP 14 · ÈRE PROFESSIONNELLE · 2000–2025</div>
      <h1 class="hero-title">
        TOP 14
        <span class="hero-highlight">SIM</span>
      </h1>
      <p class="hero-subtitle">
        La roulette tire un club et une saison. Recrute le meilleur joueur pour
        chaque poste. Compose ton XV historique et simule ta saison.
      </p>

      <!-- Modes de jeu -->
      <div class="modes">
        <RouterLink to="/draft?mode=easy" class="mode-card mode-card--easy">
          <span class="mode-icon">🎯</span>
          <h3>Facile</h3>
          <p>Notes et stats visibles. 1 skip club + 1 skip saison disponibles.</p>
          <span class="mode-cta">Jouer →</span>
        </RouterLink>
        <RouterLink to="/draft?mode=hardcore" class="mode-card mode-card--hard">
          <span class="mode-icon">💀</span>
          <h3>Hardcore</h3>
          <p>Sans notes ni stats. Ton expertise du rugby suffit. 0 skip.</p>
          <span class="mode-cta">Jouer →</span>
        </RouterLink>
      </div>

      <!-- Stats -->
      <div class="stats">
        <div class="stat">
          <span class="stat-number">14+</span>
          <span class="stat-label">Clubs</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat">
          <span class="stat-number">500+</span>
          <span class="stat-label">Joueurs</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat">
          <span class="stat-number">15</span>
          <span class="stat-label">Postes à remplir</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat">
          <span class="stat-number">∞</span>
          <span class="stat-label">Combinaisons</span>
        </div>
      </div>
    </section>

    <!-- Comment ça marche -->
    <section class="how">
      <h2 class="section-title">Comment ça marche ?</h2>
      <div class="steps">
        <div class="step">
          <div class="step-number">01</div>
          <h3>Roll</h3>
          <p>La roulette tire aléatoirement un club et une saison du Top 14.</p>
        </div>
        <div class="step-arrow">→</div>
        <div class="step">
          <div class="step-number">02</div>
          <h3>Recrute</h3>
          <p>Choisis un joueur parmi l'effectif pour compléter ton XV.</p>
        </div>
        <div class="step-arrow">→</div>
        <div class="step">
          <div class="step-number">03</div>
          <h3>Compose</h3>
          <p>Répète jusqu'à avoir 15 joueurs issus de clubs et saisons différents.</p>
        </div>
        <div class="step-arrow">→</div>
        <div class="step">
          <div class="step-number">04</div>
          <h3>Simule</h3>
          <p>Lance la simulation et découvre si ton XV peut aller chercher le Brennus.</p>
        </div>
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
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 32px;
  text-align: center;
  background: linear-gradient(135deg, #111111, #1a1500);
  border: 1px solid #2a2000;
  border-radius: 20px;
  padding: 60px 40px;
}

.hero-badge {
  background: rgba(201, 168, 76, 0.1);
  border: 1px solid rgba(201, 168, 76, 0.3);
  color: #C9A84C;
  padding: 6px 20px;
  border-radius: 999px;
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 1.5px;
}

.hero-title {
  font-size: 5rem;
  font-weight: 900;
  line-height: 1;
  letter-spacing: -2px;
  color: #ffffff;
  text-transform: uppercase;
}

.hero-highlight {
  color: #C9A84C;
  display: block;
}

.hero-subtitle {
  color: #94a3b8;
  font-size: 1.05rem;
  max-width: 560px;
  line-height: 1.8;
}

/* Modes */
.modes {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  width: 100%;
  max-width: 600px;
}

.mode-card {
  background: #111111;
  border-radius: 12px;
  padding: 28px 24px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  text-decoration: none;
  transition: all 0.2s;
  border: 1px solid #222;
}

.mode-card:hover {
  transform: translateY(-3px);
}

.mode-card--easy {
  border-color: rgba(201, 168, 76, 0.3);
}

.mode-card--easy:hover {
  border-color: #C9A84C;
  box-shadow: 0 8px 30px rgba(201, 168, 76, 0.15);
}

.mode-card--hard {
  border-color: rgba(239, 68, 68, 0.3);
}

.mode-card--hard:hover {
  border-color: #ef4444;
  box-shadow: 0 8px 30px rgba(239, 68, 68, 0.15);
}

.mode-icon { font-size: 2rem; }

.mode-card h3 {
  font-size: 1.3rem;
  font-weight: 800;
  color: #ffffff;
  text-transform: uppercase;
}

.mode-card p {
  color: #64748b;
  font-size: 0.85rem;
  line-height: 1.6;
  flex: 1;
}

.mode-cta {
  font-size: 0.85rem;
  font-weight: 600;
  color: #C9A84C;
}

.mode-card--hard .mode-cta { color: #ef4444; }

/* Stats */
.stats {
  display: flex;
  align-items: center;
  gap: 32px;
  background: rgba(0,0,0,0.3);
  border-radius: 12px;
  padding: 24px 40px;
  border: 1px solid #1a1a1a;
}

.stat {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.stat-number {
  font-size: 2rem;
  font-weight: 800;
  color: #C9A84C;
}

.stat-label {
  font-size: 0.75rem;
  color: #475569;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.stat-divider {
  width: 1px;
  height: 40px;
  background: #1e1e1e;
}

/* How it works */
.section-title {
  font-size: 1.6rem;
  font-weight: 800;
  text-align: center;
  margin-bottom: 40px;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #ffffff;
}

.steps {
  display: flex;
  align-items: center;
  gap: 0;
}

.step {
  flex: 1;
  background: #111111;
  border: 1px solid #1e1e1e;
  border-radius: 12px;
  padding: 28px 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.step-number {
  font-size: 2rem;
  font-weight: 900;
  color: rgba(201, 168, 76, 0.2);
  line-height: 1;
}

.step h3 {
  font-size: 1rem;
  font-weight: 700;
  color: #C9A84C;
  text-transform: uppercase;
}

.step p {
  color: #64748b;
  font-size: 0.85rem;
  line-height: 1.6;
}

.step-arrow {
  font-size: 1.5rem;
  color: #2a2a2a;
  padding: 0 8px;
  flex-shrink: 0;
}
</style>