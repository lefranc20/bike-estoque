<script setup>
import { ref } from 'vue'
import { baixarArquivo } from '../services/api'
import DashboardHeader from '../components/DashboardHeader.vue'

const baixandoInventario = ref(null)
const baixandoMovimentacoes = ref(null)
const erro = ref(null)

const periodo = ref({
  data_inicio: '',
  data_fim: ''
})

async function exportarInventario(formato) {
  erro.value = null
  baixandoInventario.value = formato
  try {
    await baixarArquivo(`/relatorios/inventario/${formato}`, `inventario.${formato}`)
  } catch (e) {
    erro.value = 'Erro ao gerar o relatório de inventário.'
    console.error(e)
  } finally {
    baixandoInventario.value = null
  }
}

async function exportarMovimentacoes(formato) {
  erro.value = null
  baixandoMovimentacoes.value = formato
  try {
    const params = {}
    if (periodo.value.data_inicio) params.data_inicio = periodo.value.data_inicio
    if (periodo.value.data_fim) params.data_fim = periodo.value.data_fim

    await baixarArquivo(`/relatorios/movimentacoes/${formato}`, `movimentacoes.${formato}`, params)
  } catch (e) {
    erro.value = 'Erro ao gerar o relatório de movimentações.'
    console.error(e)
  } finally {
    baixandoMovimentacoes.value = null
  }
}
</script>

<template>
  <div class="dashboard-page page-view">
    <DashboardHeader
      eyebrow="Relatórios"
      title="Exportar Relatórios"
      subtitle="Baixe o inventário atual ou o histórico de movimentações em CSV ou PDF."
    />

    <div v-if="erro" class="status-inline error">{{ erro }}</div>

    <div class="page-panel">
      <h2>Inventário atual</h2>
      <p class="status-summary">Lista completa de produtos com preço, quantidade e valor total em estoque.</p>
      <div class="form-actions">
        <button class="button-primary" :disabled="!!baixandoInventario" @click="exportarInventario('csv')">
          {{ baixandoInventario === 'csv' ? 'Gerando...' : 'Exportar CSV' }}
        </button>
        <button class="button-secondary" :disabled="!!baixandoInventario" @click="exportarInventario('pdf')">
          {{ baixandoInventario === 'pdf' ? 'Gerando...' : 'Exportar PDF' }}
        </button>
      </div>
    </div>

    <div class="page-panel">
      <h2>Movimentações por período</h2>
      <p class="status-summary">Deixe as datas em branco para exportar o histórico completo.</p>

      <div class="form-grid">
        <div class="form-field">
          <label>Data inicial</label>
          <input v-model="periodo.data_inicio" type="date" />
        </div>
        <div class="form-field">
          <label>Data final</label>
          <input v-model="periodo.data_fim" type="date" />
        </div>
      </div>

      <div class="form-actions">
        <button class="button-primary" :disabled="!!baixandoMovimentacoes" @click="exportarMovimentacoes('csv')">
          {{ baixandoMovimentacoes === 'csv' ? 'Gerando...' : 'Exportar CSV' }}
        </button>
        <button class="button-secondary" :disabled="!!baixandoMovimentacoes" @click="exportarMovimentacoes('pdf')">
          {{ baixandoMovimentacoes === 'pdf' ? 'Gerando...' : 'Exportar PDF' }}
        </button>
      </div>
    </div>
  </div>
</template>
