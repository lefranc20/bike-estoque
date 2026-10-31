<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'
import DashboardHeader from '../components/DashboardHeader.vue'
import Pager from '../components/Pager.vue'
import SortableHeader from '../components/SortableHeader.vue'

const movimentacoes = ref([])
const produtos = ref([])
const carregando = ref(true)
const erro = ref(null)
const sucesso = ref(null)
const errors = ref({})
const paginaAtual = ref(1)
const totalPaginas = ref(1)
const totalMovimentacoes = ref(0)
const ordenacao = ref({ coluna: 'created_at', direcao: 'desc' })

const form = ref({
  produto_id: '',
  tipo: 'entrada',
  quantidade: 1,
  motivo: ''
})

async function carregarDados() {
  try {
    carregando.value = true
    erro.value = null
    const [resMovimentacoes, resProdutos] = await Promise.all([
      api.get('/movimentacoes', {
        params: {
          page: paginaAtual.value,
          sort: ordenacao.value.coluna,
          direction: ordenacao.value.direcao
        }
      }),
      api.get('/produtos', { params: { per_page: 200 } })
    ])
    movimentacoes.value = resMovimentacoes.data.data
    totalPaginas.value = resMovimentacoes.data.last_page
    totalMovimentacoes.value = resMovimentacoes.data.total
    produtos.value = resProdutos.data.data
  } catch (e) {
    erro.value = 'Erro ao carregar movimentações'
    console.error(e)
  } finally {
    carregando.value = false
  }
}

function mudarPagina(pagina) {
  paginaAtual.value = pagina
  carregarDados()
}

function ordenar(coluna) {
  if (ordenacao.value.coluna === coluna) {
    ordenacao.value.direcao = ordenacao.value.direcao === 'asc' ? 'desc' : 'asc'
  } else {
    ordenacao.value.coluna = coluna
    ordenacao.value.direcao = coluna === 'created_at' ? 'desc' : 'asc'
  }
  paginaAtual.value = 1
  carregarDados()
}

async function salvarMovimentacao() {
  errors.value = {}
  sucesso.value = null
  erro.value = null

  if (!form.value.produto_id) {
    errors.value.produto_id = ['Selecione um produto']
    return
  }

  try {
    await api.post('/movimentacoes', {
      produto_id: Number(form.value.produto_id),
      tipo: form.value.tipo,
      quantidade: Number(form.value.quantidade),
      motivo: form.value.motivo
    })

    form.value = {
      produto_id: '',
      tipo: 'entrada',
      quantidade: 1,
      motivo: ''
    }
    sucesso.value = 'Movimentação registrada com sucesso.'
    await carregarDados()
    setTimeout(() => {
      sucesso.value = null
    }, 5000)
  } catch (e) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors || {}
      erro.value = e.response.data.message || 'Dados inválidos, corrija os campos.'
    } else {
      erro.value = 'Erro ao registrar movimentação'
    }
    console.error(e)
  }
}

onMounted(() => {
  carregarDados()
})
</script>

<template>
  <div class="dashboard-page page-view">
    <DashboardHeader
      eyebrow="Movimentações"
      title="Movimentações de Estoque"
      subtitle="Registre entradas, saídas e ajustes para manter o estoque sempre atualizado."
    />

    <div class="page-panel page-panel--grid">
      <div>
        <h2>Nova movimentação</h2>
        <Transition name="fade">
          <div v-if="erro" class="status-inline error">{{ erro }}</div>
        </Transition>
        <Transition name="fade">
          <div v-if="sucesso" class="status-inline success">{{ sucesso }}</div>
        </Transition>

        <div class="form-grid">
          <div class="form-field">
            <label>Produto</label>
            <select v-model="form.produto_id">
              <option value="">Selecione...</option>
              <option v-for="produto in produtos" :key="produto.id" :value="produto.id">{{ produto.nome }}</option>
            </select>
            <div v-if="errors.produto_id" class="status-inline error">{{ errors.produto_id[0] }}</div>
          </div>

          <div class="form-field">
            <label>Tipo</label>
            <select v-model="form.tipo">
              <option value="entrada">Entrada</option>
              <option value="saida">Saída</option>
              <option value="ajuste">Ajuste</option>
            </select>
          </div>

          <div class="form-field">
            <label>Quantidade</label>
            <input v-model.number="form.quantidade" type="number" :min="form.tipo === 'ajuste' ? 0 : 1" />
          </div>

          <div class="form-field form-grid-full">
            <label>Motivo</label>
            <textarea v-model="form.motivo" placeholder="Ex.: Compra, venda, correção de estoque..."></textarea>
          </div>
        </div>

        <div class="form-actions">
          <button class="button-primary" @click="salvarMovimentacao">Registrar movimentação</button>
        </div>
      </div>
    </div>

    <div class="page-panel table-card">
      <div v-if="carregando">Carregando...</div>
      <div v-else-if="erro" class="status-inline error">{{ erro }}</div>

      <table v-else>
        <thead>
          <tr>
            <SortableHeader
              coluna="created_at"
              :ordenacao-atual="ordenacao.coluna"
              :direcao-atual="ordenacao.direcao"
              @ordenar="ordenar"
            >
              Data
            </SortableHeader>
            <SortableHeader
              coluna="produto"
              :ordenacao-atual="ordenacao.coluna"
              :direcao-atual="ordenacao.direcao"
              @ordenar="ordenar"
            >
              Produto
            </SortableHeader>
            <SortableHeader
              coluna="tipo"
              :ordenacao-atual="ordenacao.coluna"
              :direcao-atual="ordenacao.direcao"
              @ordenar="ordenar"
            >
              Tipo
            </SortableHeader>
            <SortableHeader
              coluna="quantidade"
              :ordenacao-atual="ordenacao.coluna"
              :direcao-atual="ordenacao.direcao"
              @ordenar="ordenar"
            >
              Quantidade
            </SortableHeader>
            <SortableHeader
              coluna="quantidade_anterior"
              :ordenacao-atual="ordenacao.coluna"
              :direcao-atual="ordenacao.direcao"
              @ordenar="ordenar"
            >
              Anterior
            </SortableHeader>
            <SortableHeader
              coluna="quantidade_nova"
              :ordenacao-atual="ordenacao.coluna"
              :direcao-atual="ordenacao.direcao"
              @ordenar="ordenar"
            >
              Nova
            </SortableHeader>
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

      <Pager
        v-if="!carregando && !erro"
        :pagina-atual="paginaAtual"
        :total-paginas="totalPaginas"
        :total="totalMovimentacoes"
        @mudar-pagina="mudarPagina"
      />

      <p v-if="!carregando && movimentacoes.length === 0" class="status-summary">Nenhuma movimentação registrada ainda.</p>
    </div>
  </div>
</template>