<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'

const totalProdutos = ref(0)
const totalCategorias = ref(0)
const valorTotal = ref(0)
const carregando = ref(true)
const erro = ref(null)

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
    </div>

    <div class="dashboard-status">
      <div v-if="carregando" class="status-message">Carregando...</div>
      <div v-else-if="erro" class="status-error">{{ erro }}</div>
      <div v-else class="status-summary">Dados carregados com sucesso.</div>
    </div>
  </div>
</template>
