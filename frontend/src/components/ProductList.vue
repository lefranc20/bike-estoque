<script setup>
defineProps({
  produtos: {
    type: Array,
    default: () => []
  },
  carregando: {
    type: Boolean,
    default: false
  },
  erro: {
    type: String,
    default: null
  }
})

defineEmits(['delete'])
</script>

<template>
  <section class="page-panel product-list-panel table-card">
    <p class="panel-kicker">Visão geral</p>
    <h2>Listagem de Produtos</h2>
    <div v-if="carregando">Carregando...</div>
    <div v-else-if="erro" class="status-inline error">{{ erro }}</div>

    <table v-else>
      <thead>
        <tr>
          <th>Nome</th>
          <th>Código</th>
          <th>Preço</th>
          <th>Qtd</th>
          <th>Categoria</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="produto in produtos" :key="produto.id">
          <td>
            {{ produto.nome }}
            <span v-if="produto.esta_abaixo_do_minimo" class="status-inline error">Abaixo do mínimo</span>
          </td>
          <td class="numeric">{{ produto.codigo }}</td>
          <td class="numeric currency">R$ {{ Number(produto.preco).toFixed(2) }}</td>
          <td class="numeric">{{ produto.quantidade }}</td>
          <td>{{ produto.categoria?.nome }}</td>
          <td>
            <div class="table-actions">
              <button class="button-secondary" type="button" @click="$emit('delete', produto.id)">Excluir</button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <p v-if="!carregando && !erro && produtos.length === 0" class="status-summary">
      Nenhum produto cadastrado ainda.
    </p>
  </section>
</template>
