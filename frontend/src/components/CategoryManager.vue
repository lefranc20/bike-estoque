<script setup>
const form = defineModel({ type: Object, required: true })

defineProps({
  aberto: {
    type: Boolean,
    default: false
  },
  categorias: {
    type: Array,
    default: () => []
  },
  errors: {
    type: Object,
    default: () => ({})
  },
  error: {
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
  }
})

defineEmits(['save', 'delete', 'toggle'])
</script>

<template>
  <section class="page-panel category-manager-panel" :class="{ 'is-open': aberto }">
    <button class="panel-heading" type="button" :aria-expanded="aberto" @click="$emit('toggle')">
      <span>
        <span class="panel-kicker">Organização</span>
        <span class="panel-title">Nova Categoria</span>
      </span>
      <span class="panel-toggle" aria-hidden="true">{{ aberto ? '−' : '+' }}</span>
    </button>

    <Transition name="panel-expand">
      <div v-show="aberto" class="panel-content">
      <Transition name="fade">
        <div v-if="error" class="status-inline error">{{ error }}</div>
      </Transition>
      <Transition name="fade">
        <div v-if="sucesso" class="status-inline success">{{ sucesso }}</div>
      </Transition>

      <div class="form-grid">
        <div class="form-field form-grid-full">
          <label for="categoria-nome">Nome</label>
          <input id="categoria-nome" v-model="form.nome" />
          <div v-if="errors.nome" class="status-inline error">{{ errors.nome[0] }}</div>
        </div>
      </div>

      <div class="form-actions">
        <button class="button-primary" type="button" @click.stop="$emit('save')">Salvar Categoria</button>
      </div>

      <!-- Prova de conceito Tailwind (só layout/espaçamento; cores via var() do tema) -->
      <div v-if="categorias.length" class="mt-6 border-t border-[color:var(--cor-borda)] pt-4">
        <h3 class="mb-3 text-base text-[color:var(--cor-texto)]">Categorias cadastradas</h3>
        <ul class="m-0 flex list-none flex-col gap-2 p-0">
          <li
            v-for="categoria in categorias"
            :key="categoria.id"
            class="flex items-center justify-between gap-3 rounded-xl border border-[color:var(--cor-borda)] bg-[var(--cor-painel)] px-[0.9rem] py-[0.7rem] text-[color:var(--cor-texto)] transition-colors duration-200 hover:border-[rgba(147,197,253,0.3)] hover:bg-[var(--cor-fundo-alt)]"
          >
            <span class="text-[color:var(--cor-texto)]">{{ categoria.nome }}</span>
            <button
              v-if="isAdmin"
              type="button"
              class="cursor-pointer rounded-[10px] bg-slate-700 px-3 py-[0.45rem] text-slate-50 transition-colors duration-200 hover:bg-slate-600"
              @click.stop="$emit('delete', categoria.id)"
            >
              Remover
            </button>
          </li>
        </ul>
      </div>
      </div>
    </Transition>
  </section>
</template>
