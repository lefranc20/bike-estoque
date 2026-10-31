<script setup>
defineProps({
  coluna: {
    type: String,
    required: true
  },
  ordenacaoAtual: {
    type: String,
    required: true
  },
  direcaoAtual: {
    type: String,
    required: true
  },
  numeric: {
    type: Boolean,
    default: false
  }
})

defineEmits(['ordenar'])
</script>

<template>
  <th
    :class="[
      'cursor-pointer select-none transition-colors duration-200 hover:text-[color:var(--cor-texto-forte)]',
      { numeric, 'text-[color:var(--cor-texto-forte)]': ordenacaoAtual === coluna }
    ]"
    @click="$emit('ordenar', coluna)"
  >
    <span class="inline-flex items-center gap-[0.35rem]">
      <slot />
      <span v-if="ordenacaoAtual === coluna" class="text-[0.65rem]" aria-hidden="true">
        {{ direcaoAtual === 'asc' ? '▲' : '▼' }}
      </span>
    </span>
  </th>
</template>
