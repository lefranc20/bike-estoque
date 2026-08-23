<script setup>
const form = defineModel({ type: Object, required: true })

defineProps({
  aberto: {
    type: Boolean,
    default: false
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
  categorias: {
    type: Array,
    default: () => []
  }
})

defineEmits(['save', 'toggle'])
</script>

<template>
  <section class="page-panel product-form-panel" :class="{ 'is-open': aberto }">
    <button class="panel-heading" type="button" :aria-expanded="aberto" @click="$emit('toggle')">
      <span>
        <span class="panel-kicker">Estoque</span>
        <span class="panel-title">Cadastro de Novo Produto</span>
      </span>
      <span class="panel-toggle" aria-hidden="true">{{ aberto ? '−' : '+' }}</span>
    </button>

    <Transition name="panel-expand">
      <div v-show="aberto" class="panel-content">
      <div v-if="error" class="status-inline error">{{ error }}</div>
      <div v-if="sucesso" class="status-inline success">{{ sucesso }}</div>

      <div class="form-grid">
      <div class="form-field">
        <label for="produto-nome">Nome</label>
        <input id="produto-nome" v-model="form.nome" />
        <div v-if="errors.nome" class="status-inline error">{{ errors.nome[0] }}</div>
      </div>

      <div class="form-field">
        <label for="produto-codigo">Código</label>
        <input id="produto-codigo" v-model="form.codigo" />
        <div v-if="errors.codigo" class="status-inline error">{{ errors.codigo[0] }}</div>
      </div>

      <div class="form-field">
        <label for="produto-preco">Preço</label>
        <input id="produto-preco" v-model.number="form.preco" type="number" step="0.01" />
        <div v-if="errors.preco" class="status-inline error">{{ errors.preco[0] }}</div>
      </div>

      <div class="form-field">
        <label for="produto-quantidade">Quantidade</label>
        <input id="produto-quantidade" v-model.number="form.quantidade" type="number" />
        <div v-if="errors.quantidade" class="status-inline error">{{ errors.quantidade[0] }}</div>
      </div>

      <div class="form-field">
        <label for="produto-estoque-minimo">Estoque Mínimo</label>
        <input id="produto-estoque-minimo" v-model.number="form.estoque_minimo" type="number" />
        <div v-if="errors.estoque_minimo" class="status-inline error">{{ errors.estoque_minimo[0] }}</div>
      </div>

      <div class="form-field">
        <label for="produto-categoria">Categoria</label>
        <select id="produto-categoria" v-model="form.categoria_id">
          <option value="">Selecione...</option>
          <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
            {{ categoria.nome }}
          </option>
        </select>
        <div v-if="errors.categoria_id" class="status-inline error">{{ errors.categoria_id[0] || errors.categoria_id }}</div>
      </div>
      </div>

      <div class="form-field form-grid-full">
        <label for="produto-descricao">Descrição</label>
        <textarea id="produto-descricao" v-model="form.descricao"></textarea>
      </div>

      <div class="form-actions">
        <button class="button-primary" type="button" @click.stop="$emit('save')">Salvar</button>
      </div>
      </div>
    </Transition>
  </section>
</template>
