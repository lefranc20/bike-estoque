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
    :class="['th-ordenavel', { numeric, 'th-ativa': ordenacaoAtual === coluna }]"
    @click="$emit('ordenar', coluna)"
  >
    <span class="th-ordenavel-conteudo">
      <slot />
      <span v-if="ordenacaoAtual === coluna" class="th-seta" aria-hidden="true">
        {{ direcaoAtual === 'asc' ? '▲' : '▼' }}
      </span>
    </span>
  </th>
</template>

<style scoped>
.th-ordenavel {
  cursor: pointer;
  user-select: none;
  transition: color 0.2s ease;
}

.th-ordenavel:hover {
  color: var(--cor-texto-forte);
}

.th-ativa {
  color: var(--cor-texto-forte);
}

.th-ordenavel-conteudo {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
}

.th-seta {
  font-size: 0.65rem;
}
</style>
