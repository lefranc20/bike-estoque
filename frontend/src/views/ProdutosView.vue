<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const produtos = ref([])
const categorias = ref([])
const carregando = ref(true)
const erro = ref(null)
const errors = ref({})

const form = ref({
  id: null,
  nome: '',
  codigo: '',
  descricao: '',
  preco: 0,
  quantidade: 0,
  estoque_minimo: 5,
  categoria_id: null
})

const categoriaForm = ref({ nome: '' })
const categoriaErro = ref(null)
const categoriaErrors = ref({})
const categoriaSucesso = ref(null)

async function carregarDados() {
  try {
    carregando.value = true
    erro.value = null
    errors.value = {}
    const [resProdutos, resCategorias] = await Promise.all([
      api.get('/produtos'),
      api.get('/categorias')
    ])
    produtos.value = resProdutos.data
    categorias.value = resCategorias.data
  } catch (e) {
    erro.value = 'Erro ao carregar dados'
    console.error(e)
  } finally {
    carregando.value = false
  }
}

function limparFormulario() {
  form.value = {
    id: null,
    nome: '',
    codigo: '',
    descricao: '',
    preco: 0,
    quantidade: 0,
    estoque_minimo: 5,
    categoria_id: null
  }
  errors.value = {}
}

async function salvar() {
  errors.value = {}
  erro.value = null

  if (!form.value.categoria_id) {
    errors.value.categoria_id = ['Selecione uma categoria']
    return
  }

  try {
    await api.post('/produtos', form.value)
    limparFormulario()
    await carregarDados()
  } catch (e) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors || {}
      erro.value = e.response.data.message || 'Dados inválidos, corrija os campos.'
    } else {
      erro.value = 'Erro ao salvar produto'
    }
    console.error(e)
  }
}

async function salvarCategoria() {
  categoriaErro.value = null
  categoriaSucesso.value = null

  try {
    await api.post('/categorias', categoriaForm.value)
    categoriaForm.value = { nome: '', descricao: '' }
    categoriaSucesso.value = 'Categoria criada com sucesso.'
    categoriaErro.value = null
    categoriaErrors.value = {}
    await carregarDados()
  } catch (e) {
    categoriaErrors.value = {}
    if (e.response?.status === 422) {
      categoriaErro.value = e.response.data.message || 'Dados de categoria inválidos.'
      categoriaErrors.value = e.response.data.errors || {}
    } else {
      categoriaErro.value = 'Erro ao salvar categoria.'
    }
    console.error(e)
  }
}

async function excluirCategoria(id) {
  const categoria = categorias.value.find((item) => item.id === id)
  if (!categoria) return

  if (!confirm(`Tem certeza que deseja excluir a categoria "${categoria.nome}"?`)) return

  try {
    await api.delete(`/categorias/${id}`)
    categoriaSucesso.value = 'Categoria removida com sucesso.'
    categoriaErro.value = null
    categoriaErrors.value = {}
    await carregarDados()

    if (form.value.categoria_id === id) {
      form.value.categoria_id = null
    }
  } catch (e) {
    categoriaErro.value = 'Erro ao excluir categoria.'
    categoriaSucesso.value = null
    console.error(e)
  }
}

async function excluir(id) {
  if (!confirm('Tem certeza que deseja excluir este produto?')) return

  try {
    await api.delete(`/produtos/${id}`)
    await carregarDados()
  } catch (e) {
    alert('Erro ao excluir produto')
    console.error(e)
  }
}

onMounted(() => {
  carregarDados()
})
</script>

<template>
  <div class="page-view">
    <div class="page-header">
      <div>
        <h1 class="page-title">Produtos</h1>
        <p class="page-subtitle">Gerencie produtos, categorias e estoque com layout desktop consistente.</p>
      </div>
      <div class="page-toolbar">
        <a href="/">← Voltar ao Dashboard</a>
      </div>
    </div>

    <div class="page-panel page-panel--grid">
      <div>
        <h2>Novo Produto</h2>
        <div v-if="erro" class="status-inline error">{{ erro }}</div>

        <div class="form-grid">
          <div class="form-field">
            <label>Nome</label>
            <input v-model="form.nome" />
            <div v-if="errors.nome" class="status-inline error">{{ errors.nome[0] }}</div>
          </div>

          <div class="form-field">
            <label>Código</label>
            <input v-model="form.codigo" />
            <div v-if="errors.codigo" class="status-inline error">{{ errors.codigo[0] }}</div>
          </div>

          <div class="form-field">
            <label>Preço</label>
            <input v-model.number="form.preco" type="number" step="0.01" />
            <div v-if="errors.preco" class="status-inline error">{{ errors.preco[0] }}</div>
          </div>

          <div class="form-field">
            <label>Quantidade</label>
            <input v-model.number="form.quantidade" type="number" />
            <div v-if="errors.quantidade" class="status-inline error">{{ errors.quantidade[0] }}</div>
          </div>

          <div class="form-field">
            <label>Estoque Mínimo</label>
            <input v-model.number="form.estoque_minimo" type="number" />
            <div v-if="errors.estoque_minimo" class="status-inline error">{{ errors.estoque_minimo[0] }}</div>
          </div>

          <div class="form-field">
            <label>Categoria</label>
            <select v-model="form.categoria_id">
              <option value="">Selecione...</option>
              <option v-for="cat in categorias" :key="cat.id" :value="cat.id">{{ cat.nome }}</option>
            </select>
            <div v-if="errors.categoria_id" class="status-inline error">{{ errors.categoria_id[0] || errors.categoria_id }}</div>
          </div>
        </div>

        <div class="form-field form-grid-full">
          <label>Descrição</label>
          <textarea v-model="form.descricao"></textarea>
        </div>

        <div class="form-actions">
          <button class="button-primary" @click="salvar">Salvar</button>
        </div>
      </div>

      <div>
        <h2>Nova Categoria</h2>
        <div v-if="categoriaErro" class="status-inline error">{{ categoriaErro }}</div>
        <div v-if="categoriaSucesso" class="status-inline success">{{ categoriaSucesso }}</div>

        <div class="form-grid">
          <div class="form-field form-grid-full">
            <label>Nome</label>
            <input v-model="categoriaForm.nome" />
            <div v-if="categoriaErrors.nome" class="status-inline error">{{ categoriaErrors.nome[0] }}</div>
          </div>
        </div>

        <div class="form-actions">
          <button class="button-primary" @click="salvarCategoria">Salvar Categoria</button>
        </div>

        <div v-if="categorias.length" class="categoria-lista">
          <h3>Categorias cadastradas</h3>
          <ul class="categoria-lista__items">
            <li v-for="categoria in categorias" :key="categoria.id" class="categoria-item">
              <span>{{ categoria.nome }}</span>
              <button class="button-secondary" @click="excluirCategoria(categoria.id)">Remover</button>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="page-panel table-card">
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
            <td class="numeric currency">R$ {{ produto.preco.toFixed(2) }}</td>
            <td class="numeric">{{ produto.quantidade }}</td>
            <td>{{ produto.categoria?.nome }}</td>
            <td>
              <div class="table-actions">
                <button class="button-secondary" @click="excluir(produto.id)">Excluir</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <p v-if="!carregando && produtos.length === 0" class="status-summary">Nenhum produto cadastrado ainda.</p>
    </div>
  </div>
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