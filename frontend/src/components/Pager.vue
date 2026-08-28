<script setup>
defineProps({
  paginaAtual: {
    type: Number,
    required: true
  },
  totalPaginas: {
    type: Number,
    required: true
  },
  total: {
    type: Number,
    default: 0
  }
})

defineEmits(['mudar-pagina'])
</script>

<template>
  <div v-if="totalPaginas > 1" class="pager">
    <button
      class="button-secondary"
      type="button"
      :disabled="paginaAtual <= 1"
      @click="$emit('mudar-pagina', paginaAtual - 1)"
    >
      ‹ Anterior
    </button>
    <span class="pager-info">
      Página {{ paginaAtual }} de {{ totalPaginas }} · {{ total }} {{ total === 1 ? 'item' : 'itens' }}
    </span>
    <button
      class="button-secondary"
      type="button"
      :disabled="paginaAtual >= totalPaginas"
      @click="$emit('mudar-pagina', paginaAtual + 1)"
    >
      Próxima ›
    </button>
  </div>
</template>

<style scoped>
.pager {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-top: 1.25rem;
}

.pager-info {
  color: #94a3b8;
  font-size: 0.85rem;
}
</style>
