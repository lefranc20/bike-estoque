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
  <div style="padding: 40px; font-family: Arial; max-width: 1100px; margin: 0 auto; color: #222;">
    <h1>Movimentações de Estoque</h1>

    <p>
      <a href="/" style="color: #4CAF50;">← Voltar ao Dashboard</a> |
      <a href="/produtos" style="color: #4CAF50;">Ir para Produtos</a>
    </p>

    <div v-if="carregando">Carregando...</div>
    <div v-else-if="erro" style="color: red;">{{ erro }}</div>

    <table v-else style="width: 100%; border-collapse: collapse; margin-top: 20px;">
      <thead>
        <tr style="background: #333; color: white;">
          <th style="padding: 10px; text-align: left;">Data</th>
          <th style="padding: 10px;">Produto</th>
          <th style="padding: 10px;">Tipo</th>
          <th style="padding: 10px;">Quantidade</th>
          <th style="padding: 10px;">Anterior</th>
          <th style="padding: 10px;">Nova</th>
          <th style="padding: 10px;">Motivo</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="mov in movimentacoes" :key="mov.id" style="border-bottom: 1px solid #ddd;">
          <td style="padding: 10px;">
            {{ new Date(mov.created_at).toLocaleString('pt-BR') }}
          </td>
          <td style="padding: 10px;">{{ mov.produto?.nome || '—' }}</td>
          <td style="padding: 10px; text-align: center;">
            <span v-if="mov.tipo === 'entrada'" style="color: green; font-weight: bold;">Entrada</span>
            <span v-else-if="mov.tipo === 'saida'" style="color: red; font-weight: bold;">Saída</span>
            <span v-else style="color: orange; font-weight: bold;">Ajuste</span>
          </td>
          <td style="padding: 10px; text-align: center;">{{ mov.quantidade }}</td>
          <td style="padding: 10px; text-align: center;">{{ mov.quantidade_anterior }}</td>
          <td style="padding: 10px; text-align: center;">{{ mov.quantidade_nova }}</td>
          <td style="padding: 10px;">{{ mov.motivo || '—' }}</td>
        </tr>
      </tbody>
    </table>

    <p v-if="!carregando && movimentacoes.length === 0" style="margin-top: 20px; color: #666;">
      Nenhuma movimentação registrada ainda.
    </p>
  </div>
</template>