<script setup>
import Pager from './Pager.vue'

defineProps({
  aberto: {
    type: Boolean,
    default: false
  },
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
  },
  sucesso: {
    type: String,
    default: null
  },
  isAdmin: {
    type: Boolean,
    default: false
  },
  paginaAtual: {
    type: Number,
    default: 1
  },
  totalPaginas: {
    type: Number,
    default: 1
  },
  total: {
    type: Number,
    default: 0
  }
})

defineEmits(['delete', 'toggle', 'mudar-pagina'])
</script>

<template>
  <section class="page-panel product-list-panel table-card" :class="{ 'is-open': aberto }">
    <button class="panel-heading" type="button" :aria-expanded="aberto" @click="$emit('toggle')">
      <span>
        <span class="panel-kicker">Visão geral</span>
        <span class="panel-title">Listagem de Produtos</span>
      </span>
      <span class="panel-toggle" aria-hidden="true">{{ aberto ? '−' : '+' }}</span>
    </button>

    <Transition name="panel-expand">
      <div v-show="aberto" class="panel-content">
      <Transition name="fade">
        <div v-if="sucesso" class="status-inline success">{{ sucesso }}</div>
      </Transition>

      <div v-if="carregando">Carregando...</div>
      <div v-else-if="erro" class="status-inline error">{{ erro }}</div>

      <table v-else>
      <thead>
        <tr>
          <th>Nome</th>
          <th class="numeric">Código</th>
          <th class="numeric">Preço</th>
          <th class="numeric">Qtd</th>
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
            <div v-if="isAdmin" class="table-actions">
              <button class="button-secondary" type="button" @click="$emit('delete', produto.id)">Excluir</button>
            </div>
            <span v-else class="status-inline">—</span>
          </td>
        </tr>
      </tbody>
      </table>

      <p v-if="!carregando && !erro && produtos.length === 0" class="status-summary">
        Nenhum produto cadastrado ainda.
      </p>

      <Pager
        v-if="!carregando && !erro"
        :pagina-atual="paginaAtual"
        :total-paginas="totalPaginas"
        :total="total"
        @mudar-pagina="$emit('mudar-pagina', $event)"
      />
      </div>
    </Transition>
  </section>
</template>
