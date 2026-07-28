<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const totalProdutos = ref(0)
const totalCategorias = ref(0)
const valorTotal = ref(0)
const carregando = ref(true)
const erro = ref(null)

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
  <div style="padding: 40px; font-family: Arial;">
    <p>
      <a href="/produtos">Ir para Produtos →</a>
    </p>
    <h1>Controle de Estoque - Peças de Bicicleta</h1>

    <div v-if="carregando">Carregando...</div>

    <div v-else-if="erro" style="color: red;">
      {{ erro }}
    </div>

    <div v-else>
      <p><strong>Total de Produtos:</strong> {{ totalProdutos }}</p>
      <p><strong>Total de Categorias:</strong> {{ totalCategorias }}</p>
      <p><strong>Valor Total em Estoque:</strong> R$ {{ valorTotal }}</p>
    </div>
  </div>
</template>
