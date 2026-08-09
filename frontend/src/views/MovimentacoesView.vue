<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const movimentacoes = ref([])
const carregando = ref(true)
const erro = ref(null)

async function carregarMovimentacoes() {
  try {
    carregando.value = true
    const resposta = await api.get('/movimentacoes')
    movimentacoes.value = resposta.data
  } catch (e) {
    erro.value = 'Erro ao carregar movimentações'
    console.error(e)
  } finally {
    carregando.value = false
  }
}

onMounted(() => {
  carregarMovimentacoes()
})
</script>

<template>
  <div class="page-shell">
    <div class="page-header">
      <div>
        <h1 class="page-title">Movimentações de Estoque</h1>
        <p class="page-subtitle">Acompanhe entradas, saídas e ajustes em um painel uniforme.</p>
      </div>
      <div class="page-toolbar">
        <a href="/">← Voltar ao Dashboard</a>
        <a href="/produtos">Ir para Produtos</a>
      </div>
    </div>

    <div class="page-panel table-card">
      <div v-if="carregando">Carregando...</div>
      <div v-else-if="erro" class="status-inline error">{{ erro }}</div>

      <table v-else>
        <thead>
          <tr>
            <th>Data</th>
            <th>Produto</th>
            <th>Tipo</th>
            <th>Quantidade</th>
            <th>Anterior</th>
            <th>Nova</th>
            <th>Motivo</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="mov in movimentacoes" :key="mov.id">
            <td>{{ new Date(mov.created_at).toLocaleString('pt-BR') }}</td>
            <td>{{ mov.produto?.nome || '—' }}</td>
            <td>
              <span v-if="mov.tipo === 'entrada'" class="status-inline success">Entrada</span>
              <span v-else-if="mov.tipo === 'saida'" class="status-inline error">Saída</span>
              <span v-else class="status-inline">Ajuste</span>
            </td>
            <td>{{ mov.quantidade }}</td>
            <td>{{ mov.quantidade_anterior }}</td>
            <td>{{ mov.quantidade_nova }}</td>
            <td>{{ mov.motivo || '—' }}</td>
          </tr>
        </tbody>
      </table>

      <p v-if="!carregando && movimentacoes.length === 0" class="status-summary">Nenhuma movimentação registrada ainda.</p>
    </div>
  </div>
</template>