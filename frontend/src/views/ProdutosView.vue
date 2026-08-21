<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'
import DashboardHeader from '../components/DashboardHeader.vue'
import ProductForm from '../components/ProductForm.vue'
import CategoryManager from '../components/CategoryManager.vue'
import ProductList from '../components/ProductList.vue'

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
  <div class="dashboard-page page-view">
    <DashboardHeader
      eyebrow="Produtos"
      title="Produtos"
      subtitle="Gerencie produtos, categorias e estoque com layout desktop consistente."
    />

    <div class="product-sections">
      <ProductForm
        v-model="form"
        :categorias="categorias"
        :errors="errors"
        :error="erro"
        @save="salvar"
      />
      <CategoryManager
        v-model="categoriaForm"
        :categorias="categorias"
        :errors="categoriaErrors"
        :error="categoriaErro"
        :sucesso="categoriaSucesso"
        @save="salvarCategoria"
        @delete="excluirCategoria"
      />
    </div>

    <ProductList
      :produtos="produtos"
      :carregando="carregando"
      :erro="erro"
      @delete="excluir"
    />
  </div>
</template>
