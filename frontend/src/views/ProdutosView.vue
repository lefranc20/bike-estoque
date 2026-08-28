<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'
import DashboardHeader from '../components/DashboardHeader.vue'
import ProductForm from '../components/ProductForm.vue'
import CategoryManager from '../components/CategoryManager.vue'
import ProductList from '../components/ProductList.vue'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()

const produtos = ref([])
const categorias = ref([])
const carregando = ref(true)
const erro = ref(null)
const errors = ref({})
const produtoSucesso = ref(null)
const paginaAtual = ref(1)
const totalPaginasProdutos = ref(1)
const totalProdutosCount = ref(0)

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
const cardAberto = ref(null)

function alternarCard(card) {
  cardAberto.value = cardAberto.value === card ? null : card
}

async function carregarDados() {
  try {
    carregando.value = true
    erro.value = null
    errors.value = {}
    const [resProdutos, resCategorias] = await Promise.all([
      api.get('/produtos', { params: { page: paginaAtual.value } }),
      api.get('/categorias')
    ])
    categorias.value = resCategorias.data

    if (resProdutos.data.data.length === 0 && paginaAtual.value > 1) {
      paginaAtual.value -= 1
      return await carregarDados()
    }

    produtos.value = resProdutos.data.data
    totalPaginasProdutos.value = resProdutos.data.last_page
    totalProdutosCount.value = resProdutos.data.total
  } catch (e) {
    erro.value = 'Erro ao carregar dados'
    console.error(e)
  } finally {
    carregando.value = false
  }
}

function mudarPaginaProdutos(pagina) {
  paginaAtual.value = pagina
  carregarDados()
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
    produtoSucesso.value = 'Produto salvo com sucesso.'
    setTimeout(() => {
      produtoSucesso.value = null
    }, 5000)
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
    setTimeout(() => {
      categoriaSucesso.value = null
    }, 5000)
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
    setTimeout(() => {
      categoriaSucesso.value = null
    }, 5000)

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
    produtoSucesso.value = 'Produto removido com sucesso.'
    setTimeout(() => {
      produtoSucesso.value = null
    }, 5000)
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
      subtitle="Gerencie produtos, categorias e estoque com um layout consistente em qualquer tela."
    />

    <div class="product-sections" :class="{ 'has-open-card': cardAberto }">
      <ProductForm
        v-model="form"
        :categorias="categorias"
        :errors="errors"
        :error="erro"
        :sucesso="produtoSucesso"
        :aberto="cardAberto === 'produto'"
        @save="salvar"
        @toggle="alternarCard('produto')"
      />
      <CategoryManager
        v-model="categoriaForm"
        :categorias="categorias"
        :errors="categoriaErrors"
        :error="categoriaErro"
        :sucesso="categoriaSucesso"
        :is-admin="auth.isAdmin"
        :aberto="cardAberto === 'categoria'"
        @save="salvarCategoria"
        @delete="excluirCategoria"
        @toggle="alternarCard('categoria')"
      />
      <ProductList
        :produtos="produtos"
        :carregando="carregando"
        :erro="erro"
        :sucesso="produtoSucesso"
        :is-admin="auth.isAdmin"
        :aberto="cardAberto === 'produtos'"
        :pagina-atual="paginaAtual"
        :total-paginas="totalPaginasProdutos"
        :total="totalProdutosCount"
        @delete="excluir"
        @toggle="alternarCard('produtos')"
        @mudar-pagina="mudarPaginaProdutos"
      />
    </div>
  </div>
</template>
