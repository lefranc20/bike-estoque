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

const mouseX = ref(0.5)
const mouseY = ref(0.5)

function onMouseMove(evento) {
  const rect = evento.currentTarget.getBoundingClientRect()
  mouseX.value = (evento.clientX - rect.left) / rect.width
  mouseY.value = (evento.clientY - rect.top) / rect.height
}

function onMouseLeave() {
  mouseX.value = 0.5
  mouseY.value = 0.5
}

function parallax(profundidade) {
  const dx = (mouseX.value - 0.5) * 90 * profundidade
  const dy = (mouseY.value - 0.5) * 50 * profundidade
  return { transform: `translate(${dx}px, ${dy}px)` }
}

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
  <div class="dashboard-page" @mousemove="onMouseMove" @mouseleave="onMouseLeave">
    <DashboardHeader
      eyebrow="Visão geral"
      title="Controle de Estoque"
      subtitle="Visualize os principais indicadores do estoque em um painel estável, claro e otimizado para desktop."
    />

    <div class="dashboard-hero">
      <div class="blob-wrap" :style="parallax(0.6)">
        <div class="blob blob-a"></div>
      </div>
      <div class="blob-wrap" :style="parallax(1.2)">
        <div class="blob blob-b"></div>
      </div>

      <div class="dashboard-hero-content">
        <p class="card-label">Valor Total em Estoque</p>
        <p class="card-value">{{ valorTotalFormatado }}</p>
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
      <div class="dashboard-card">
        <p class="card-label">Produtos com estoque abaixo do mínimo</p>
        <p class="card-value">{{ produtosAbaixoDoMinimo.length }}</p>
        <button class="button-secondary" @click="mostrarProdutosAbaixoDoMinimo = !mostrarProdutosAbaixoDoMinimo">
          {{ mostrarProdutosAbaixoDoMinimo ? 'Ocultar lista' : 'Ver produtos' }}
        </button>
      </div>
    </div>

    <div v-if="mostrarProdutosAbaixoDoMinimo" class="dashboard-status alert-panel">
      <h3 class="alert-panel-title">
        <span class="alert-icon">!</span> Produtos abaixo do mínimo
      </h3>
      <div v-if="produtosAbaixoDoMinimo.length" class="table-card">
        <table>
          <thead>
            <tr>
              <th>Produto</th>
              <th class="numeric">Estoque</th>
              <th class="numeric">Mínimo</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="produto in produtosAbaixoDoMinimo" :key="produto.id">
              <td>{{ produto.nome }}</td>
              <td class="numeric">{{ produto.quantidade }}</td>
              <td class="numeric">{{ produto.estoque_minimo }}</td>
            </tr>
          </tbody>
        </table>
      </div>
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
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, #1d9bf0 0%, #0ea5e9 18%, #3b82f6 100%);
  border-radius: 22px;
  box-shadow: 0 22px 48px rgba(37, 99, 235, 0.24);
  padding: 2rem 2.2rem;
  margin-bottom: 1.8rem;
}

.blob-wrap {
  position: absolute;
  inset: 0;
  pointer-events: none;
  transition: transform 0.35s ease-out;
}

.blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(55px);
  opacity: 0.45;
  animation: float ease-in-out infinite;
}

.blob-a {
  width: 200px;
  height: 200px;
  top: -120px;
  left: 15%;
  background: #ffffff;
  animation-duration: 10s;
}

.blob-b {
  width: 200px;
  height: 200px;
  bottom: -120px;
  right: 8%;
  background: #f472b6;
  animation-duration: 13s;
  animation-delay: -5s;
}

@keyframes float {
  0%, 100% {
    transform: translate(0, 0) scale(1);
  }
  50% {
    transform: translate(16px, -12px) scale(1.08);
  }
}

.dashboard-hero-content {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
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

.alert-panel {
  border-color: rgba(248, 113, 113, 0.28);
  background: rgba(127, 29, 29, 0.14);
}

.alert-panel-title {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  margin: 0 0 1rem;
  color: #fecaca;
  font-size: 1rem;
  font-weight: 700;
}

.alert-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 1.5rem;
  height: 1.5rem;
  flex-shrink: 0;
  border-radius: 50%;
  background: rgba(248, 113, 113, 0.18);
  color: #f87171;
  font-size: 0.85rem;
  font-weight: 800;
}

.alert-panel .table-card {
  background: rgba(15, 23, 42, 0.6);
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
