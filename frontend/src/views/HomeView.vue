<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'

const totalProdutos = ref(0)
const totalCategorias = ref(0)
const valorTotal = ref(0)
const produtosAbaixoDoMinimo = ref([])
const mostrarProdutosAbaixoDoMinimo = ref(false)
const carregando = ref(true)
const erro = ref(null)
const sucessoVisivel = ref(false)

const valorTotalFormatado = computed(() => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
    minimumFractionDigits: 2,
  }).format(valorTotal.value || 0)
})

onMounted(async () => {
  try {
    const resposta = await api.get('/dashboard')
    totalProdutos.value = resposta.data.total_produtos
    totalCategorias.value = resposta.data.total_categorias
    valorTotal.value = resposta.data.valor_total_estoque
    produtosAbaixoDoMinimo.value = resposta.data.produtos_abaixo_do_minimo
    sucessoVisivel.value = true
    setTimeout(() => {
      sucessoVisivel.value = false
    }, 5000)
  } catch (e) {
    erro.value = 'Não foi possível conectar com o backend'
    console.error(e)
  } finally {
    carregando.value = false
  }
})
</script>

<template>
  <div class="dashboard-page">
    <div class="dashboard-header">
      <div class="dashboard-title-group">
        <p class="dashboard-breadcrumb">
          <span>Visão geral</span>
          <span class="breadcrumb-separator">•</span>
          <span>Desktop</span>
        </p>
        <h1>Controle de Estoque</h1>
        <p class="dashboard-subtitle">Visualize os principais indicadores do estoque em um painel estável, claro e otimizado para desktop.</p>
      </div>
      <div class="dashboard-actions">
        <a href="/produtos">Produtos</a>
        <a href="/movimentacoes">Movimentações</a>
      </div>
    </div>

    <div class="dashboard-grid">
      <div class="dashboard-card">
        <p class="card-label">Total de Produtos</p>
        <p class="card-value">{{ totalProdutos }}</p>
      </div>
      <div class="dashboard-card">
        <p class="card-label">Total de Categorias</p>
        <p class="card-value">{{ totalCategorias }}</p>
      </div>
      <div class="dashboard-card card-accent">
        <p class="card-label">Valor Total em Estoque</p>
        <p class="card-value">{{ valorTotalFormatado }}</p>
      </div>
      <div class="dashboard-card">
        <p class="card-label">Produtos com estoque abaixo do mínimo</p>
        <p class="card-value">{{ produtosAbaixoDoMinimo.length }}</p>
        <button class="button-secondary" @click="mostrarProdutosAbaixoDoMinimo = !mostrarProdutosAbaixoDoMinimo">
          {{ mostrarProdutosAbaixoDoMinimo ? 'Ocultar lista' : 'Ver produtos' }}
        </button>
      </div>
    </div>

    <div v-if="mostrarProdutosAbaixoDoMinimo" class="dashboard-status">
      <h3 class="status-summary">Produtos abaixo do mínimo</h3>
      <ul v-if="produtosAbaixoDoMinimo.length" class="status-summary">
        <li v-for="produto in produtosAbaixoDoMinimo" :key="produto.id">
          {{ produto.nome }} — estoque: {{ produto.quantidade }} (mínimo: {{ produto.estoque_minimo }})
        </li>
      </ul>
      <p v-else class="status-summary">Nenhum produto está abaixo do mínimo.</p>
    </div>

    <div
      v-if="carregando || erro || sucessoVisivel"
      class="dashboard-status"
      :class="{ 'status-success-container': sucessoVisivel && !carregando && !erro }"
    >
      <div v-if="carregando" class="status-message">Carregando...</div>
      <div v-else-if="erro" class="status-error">{{ erro }}</div>
      <div v-else class="status-summary status-success">Dados carregados com sucesso.</div>
    </div>
  </div>
</template>

<style scoped>
.dashboard-page {
  width: min(100%, 1500px);
  max-width: 1500px;
  margin: 0 auto;
  padding: 72px 64px 48px;
  color: #e2e8f0;
  box-sizing: border-box;
}

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
  color: #93c5fd;
  opacity: 0.9;
}

.breadcrumb-separator {
  opacity: 0.5;
}

.dashboard-header h1 {
  margin: 0;
  font-size: clamp(2.3rem, 3vw, 3rem);
  font-weight: 700;
  color: #f8fafc;
  line-height: 1.1;
}

.dashboard-subtitle {
  margin-top: 0.8rem;
  color: #cbd5e1;
  font-size: 1.05rem;
  line-height: 1.7;
  max-width: 720px;
}

.dashboard-actions {
  display: flex;
  gap: 0.9rem;
  flex-wrap: wrap;
  padding-top: 0.5rem;
}

.dashboard-actions a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 120px;
  padding: 0.8rem 1.2rem;
  border-radius: 999px;
  background: rgba(148, 163, 184, 0.08);
  border: 1px solid rgba(148, 163, 184, 0.18);
  color: #e2e8f0;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.2s ease;
}

.dashboard-actions a:hover {
  background: rgba(59, 130, 246, 0.12);
  border-color: rgba(96, 165, 250, 0.35);
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 1.75fr 1fr;
  gap: 1.8rem;
  margin-bottom: 1.6rem;
  align-items: stretch;
}

.dashboard-card {
  background: rgba(15, 23, 42, 0.92);
  border: 1px solid rgba(148, 163, 184, 0.14);
  border-radius: 22px;
  box-shadow: 0 18px 40px rgba(15, 23, 42, 0.22);
  padding: 1.6rem 1.7rem;
  min-height: 190px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.dashboard-card .card-label {
  margin: 0 0 1rem;
  font-size: 0.8rem;
  letter-spacing: 0.13em;
  text-transform: uppercase;
  color: #cbd5e1;
  opacity: 0.9;
}

.dashboard-card .card-value {
  margin: 0;
  font-size: clamp(2.3rem, 4vw, 4rem);
  font-weight: 700;
  color: #f8fafc;
  line-height: 1;
}

.card-accent {
  grid-column: 1 / 2;
  min-height: 220px;
  background: linear-gradient(135deg, #1d9bf0 0%, #0ea5e9 18%, #3b82f6 100%);
  border: none;
  box-shadow: 0 22px 48px rgba(37, 99, 235, 0.24);
  min-width: 0;
}

.dashboard-card:nth-child(4) {
  grid-column: 2 / 3;
  min-height: 220px;
}

@media (min-width: 1100px) {
  .dashboard-grid {
    grid-template-columns: 1.9fr 1fr;
  }

  .card-accent {
    grid-column: 1 / 2;
    min-height: 240px;
  }
}

.card-accent .card-label,
.card-accent .card-value {
  color: #f8fbff;
}

.dashboard-card:nth-child(4) {
  display: flex;
  align-items: center;
  justify-content: center;
}

.dashboard-card:nth-child(4) .card-value {
  font-size: clamp(2.2rem, 3vw, 3.2rem);
  margin-bottom: 1rem;
}

.button-secondary {
  align-self: center;
  min-width: 140px;
  padding: 0.7rem 1rem;
  border: 1px solid rgba(148, 163, 184, 0.2);
  border-radius: 12px;
  background: rgba(15, 23, 42, 0.35);
  color: #e2e8f0;
  font-weight: 600;
  cursor: pointer;
}

.dashboard-status {
  width: 100%;
  padding: 1.2rem 1.25rem;
  border-radius: 18px;
  border: 1px solid rgba(148, 163, 184, 0.14);
  background: rgba(15, 23, 42, 0.82);
  color: #cbd5e1;
  margin-top: 0.8rem;
}

.status-summary {
  margin: 0;
  color: #cbd5e1;
  line-height: 1.6;
}

.status-summary ul {
  margin: 0.75rem 0 0;
  padding-left: 1.25rem;
}

.status-summary li + li {
  margin-top: 0.35rem;
}

.status-message,
.status-error {
  margin: 0;
}

.status-error {
  color: #f87171;
}

.status-success {
  color: #4ade80;
}

.status-success-container {
  animation: fade-out 5s ease forwards;
}

@keyframes fade-out {
  from {
    opacity: 1;
  }

  to {
    opacity: 0;
  }
}
</style>
