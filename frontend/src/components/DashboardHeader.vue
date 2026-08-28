<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useTemaStore } from '../stores/tema'

const auth = useAuthStore()
const tema = useTemaStore()
const router = useRouter()

async function sair() {
  await auth.logout()
  router.push('/login')
}

defineProps({
  eyebrow: {
    type: String,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    required: true
  }
})

const dispositivo = ref('Desktop')
const mobileQuery = window.matchMedia('(max-width: 760px)')

function atualizarDispositivo(evento) {
  dispositivo.value = evento.matches ? 'Mobile' : 'Desktop'
}

onMounted(() => {
  atualizarDispositivo(mobileQuery)
  mobileQuery.addEventListener('change', atualizarDispositivo)
})

onUnmounted(() => {
  mobileQuery.removeEventListener('change', atualizarDispositivo)
})
</script>

<template>
  <div class="dashboard-header">
    <div class="dashboard-title-group">
      <p class="dashboard-breadcrumb">
        <span>{{ eyebrow }}</span>
        <span class="breadcrumb-separator">•</span>
        <span>{{ dispositivo }}</span>
        <template v-if="auth.user">
          <span class="breadcrumb-separator">•</span>
          <span>{{ auth.user.name }}</span>
        </template>
      </p>
      <h1>{{ title }}</h1>
      <p class="dashboard-subtitle">{{ subtitle }}</p>
    </div>
    <nav class="dashboard-actions" aria-label="Navegação principal">
      <RouterLink to="/">Dashboard</RouterLink>
      <RouterLink to="/produtos">Produtos</RouterLink>
      <RouterLink to="/movimentacoes">Movimentações</RouterLink>
      <RouterLink to="/relatorios">Relatórios</RouterLink>
      <button
        class="tema-toggle"
        type="button"
        :aria-label="tema.tema === 'dark' ? 'Ativar tema claro' : 'Ativar tema escuro'"
        :title="tema.tema === 'dark' ? 'Ativar tema claro' : 'Ativar tema escuro'"
        @click="tema.alternar()"
      >
        {{ tema.tema === 'dark' ? '☀️' : '🌙' }}
      </button>
      <button class="logout-button" type="button" @click="sair">Sair</button>
    </nav>
  </div>
</template>

<style scoped>
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1.5rem;
  margin-bottom: 2.5rem;
}

.dashboard-title-group {
  max-width: 720px;
}

.dashboard-breadcrumb {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  margin: 0 0 0.8rem;
  font-size: 0.72rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--cor-link);
  opacity: 0.9;
}

.breadcrumb-separator {
  opacity: 0.5;
}

.dashboard-header h1 {
  margin: 0;
  font-size: clamp(2.3rem, 3vw, 3rem);
  font-weight: 700;
  color: var(--cor-texto-forte);
  line-height: 1.1;
}

.dashboard-subtitle {
  margin-top: 0.8rem;
  color: var(--cor-texto-medio);
  font-size: 1.05rem;
  line-height: 1.7;
  max-width: 720px;
}

.dashboard-actions {
  display: flex;
  align-items: center;
  gap: 0.9rem;
  flex-wrap: wrap;
  flex-shrink: 0;
  padding-top: 0.5rem;
}

.dashboard-actions button {
  min-width: auto;
  padding: 0.8rem 1.2rem;
  border-radius: 999px;
}

.logout-button {
  background: rgba(239, 68, 68, 0.12);
  border: 1px solid rgba(248, 113, 113, 0.4);
  color: var(--cor-erro-texto);
  font-family: inherit;
  font-size: 0.9375rem;
  line-height: 1.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.logout-button:hover {
  background: rgba(239, 68, 68, 0.22);
  border-color: rgba(248, 113, 113, 0.6);
}

.dashboard-actions a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 120px;
  padding: 0.8rem 1.2rem;
  border-radius: 999px;
  background: var(--cor-chip-fundo);
  border: 1px solid var(--cor-borda-forte);
  color: var(--cor-texto);
  text-decoration: none;
  font-weight: 600;
  transition: all 0.2s ease;
}

.dashboard-actions a:hover {
  background: rgba(59, 130, 246, 0.12);
  border-color: rgba(96, 165, 250, 0.35);
}

.dashboard-actions a.router-link-exact-active {
  background: rgba(37, 99, 235, 0.24);
  border-color: rgba(96, 165, 250, 0.5);
  color: var(--cor-link);
}

.tema-toggle {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  min-width: 0;
  padding: 0;
  border-radius: 999px;
  background: var(--cor-chip-fundo);
  border: 1px solid var(--cor-borda-forte);
  color: var(--cor-texto);
  font-size: 1.2rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.tema-toggle:hover {
  background: rgba(59, 130, 246, 0.12);
  border-color: rgba(96, 165, 250, 0.35);
  transform: translateY(-1px);
}

@media (max-width: 760px) {
  .dashboard-header {
    flex-direction: column;
    align-items: stretch;
  }

  .dashboard-actions {
    justify-content: flex-start;
  }
}
</style>
