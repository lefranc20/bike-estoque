<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'
import DashboardHeader from '../components/DashboardHeader.vue'

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
    <DashboardHeader
      eyebrow="Visão geral"
      title="Controle de Estoque"
      subtitle="Visualize os principais indicadores do estoque em um painel estável, claro e otimizado para desktop."
    />

    <div class="dashboard-hero">
      <p class="card-label">Valor Total em Estoque</p>
      <p class="card-value">{{ valorTotalFormatado }}</p>
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
      <div class="dashboard-card">
        <p class="card-label">Produtos com estoque abaixo do mínimo</p>
        <p class="card-value">{{ produtosAbaixoDoMinimo.length }}</p>
        <button class="button-secondary" @click="mostrarProdutosAbaixoDoMinimo = !mostrarProdutosAbaixoDoMinimo">
          {{ mostrarProdutosAbaixoDoMinimo ? 'Ocultar lista' : 'Ver produtos' }}
        </button>
      </div>
    </div>

    <div v-if="mostrarProdutosAbaixoDoMinimo" class="dashboard-status">
      <template v-if="produtosAbaixoDoMinimo.length">
        <h3 class="status-summary">Produtos abaixo do mínimo</h3>
        <ul class="status-summary">
        <li v-for="produto in produtosAbaixoDoMinimo" :key="produto.id">
          {{ produto.nome }} — estoque: {{ produto.quantidade }} (mínimo: {{ produto.estoque_minimo }})
        </li>
        </ul>
      </template>
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

.dashboard-hero {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  background: linear-gradient(135deg, #1d9bf0 0%, #0ea5e9 18%, #3b82f6 100%);
  border-radius: 22px;
  box-shadow: 0 22px 48px rgba(37, 99, 235, 0.24);
  padding: 2rem 2.2rem;
  margin-bottom: 1.8rem;
}

.dashboard-hero .card-label,
.dashboard-hero .card-value {
  color: #f8fbff;
}

.dashboard-hero .card-value {
  font-size: clamp(2.4rem, 4.5vw, 3.6rem);
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1.8rem;
  margin-bottom: 1.6rem;
  align-items: stretch;
}

.dashboard-card {
  background: rgba(15, 23, 42, 0.92);
  border: 1px solid rgba(148, 163, 184, 0.14);
  border-radius: 22px;
  box-shadow: 0 18px 40px rgba(15, 23, 42, 0.22);
  padding: 1.8rem 1.9rem;
  display: flex;
  flex-direction: column;
}

.dashboard-card .card-label {
  margin: 0 0 0.9rem;
  font-size: 0.8rem;
  letter-spacing: 0.13em;
  text-transform: uppercase;
  color: #cbd5e1;
  opacity: 0.9;
}

.dashboard-card .card-value {
  margin: 0;
  font-size: clamp(2rem, 2.6vw, 2.6rem);
  font-weight: 700;
  color: #f8fafc;
  line-height: 1;
}

.dashboard-card .button-secondary {
  align-self: flex-start;
  margin-top: 1.2rem;
  min-width: 140px;
  padding: 0.7rem 1rem;
  border: 1px solid rgba(148, 163, 184, 0.2);
  border-radius: 12px;
  background: rgba(15, 23, 42, 0.35);
  color: #e2e8f0;
  font-weight: 600;
  cursor: pointer;
}

@media (max-width: 900px) {
  .dashboard-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 620px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
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
