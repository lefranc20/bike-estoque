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
      <div v-if="error" class="status-inline error">{{ error }}</div>
      <div v-if="sucesso" class="status-inline success">{{ sucesso }}</div>

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

      <div v-if="categorias.length" class="categoria-lista">
        <h3>Categorias cadastradas</h3>
        <ul class="categoria-lista__items">
          <li v-for="categoria in categorias" :key="categoria.id" class="categoria-item">
            <span>{{ categoria.nome }}</span>
            <button
              v-if="isAdmin"
              class="button-secondary"
              type="button"
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

<style scoped>
.categoria-lista {
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid rgba(148, 163, 184, 0.12);
}

.categoria-lista h3 {
  margin-bottom: 0.75rem;
  font-size: 1rem;
  color: #e2e8f0;
}

.categoria-lista__items {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.categoria-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 0.75rem;
  padding: 0.7rem 0.9rem;
  border: 1px solid rgba(148, 163, 184, 0.14);
  border-radius: 12px;
  background: rgba(15, 23, 42, 0.7);
  color: #e2e8f0;
}

.categoria-item span {
  color: #e2e8f0;
}

.categoria-item button {
  padding: 0.45rem 0.75rem;
  background: #334155;
  color: #f8fafc;
  border-radius: 10px;
}
</style>
