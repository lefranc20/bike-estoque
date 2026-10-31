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
  <div v-if="totalPaginas > 1" class="flex flex-wrap items-center justify-center gap-4 mt-5">
    <button
      class="button-secondary"
      type="button"
      :disabled="paginaAtual <= 1"
      @click="$emit('mudar-pagina', paginaAtual - 1)"
    >
      ‹ Anterior
    </button>
    <span class="text-[0.85rem] text-[color:var(--cor-texto-fraco)]">
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
