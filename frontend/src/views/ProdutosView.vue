<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const produtos = ref([])
const categorias = ref([])
const carregando = ref(true)
const erro = ref(null)

const form = ref({
  id: null,
  nome: '',
  codigo: '',
  descricao: '',
  preco: 0,
  quantidade: 0,
  estoque_minimo: 5,
  categoria_id: ''
})

const editando = ref(false)

async function carregarDados() {
  try {
    carregando.value = true
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
    categoria_id: ''
  }
  editando.value = false
}

async function salvar() {
  try {
    if (editando.value) {
      await api.put(`/produtos/${form.value.id}`, form.value)
    } else {
      await api.post('/produtos', form.value)
    }
    limparFormulario()
    await carregarDados()
  } catch (e) {
    alert('Erro ao salvar produto')
    console.error(e)
  }
}

function editar(produto) {
  form.value = { ...produto }
  editando.value = true
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
  <div style="padding: 40px; font-family: Arial; max-width: 1000px; margin: 0 auto;">
    <h1>Produtos - Peças de Bicicleta</h1>

    <p>
      <a href="/">← Voltar ao Dashboard</a>
    </p>

    <!-- Formulário -->
    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
      <h2>{{ editando ? 'Editar Produto' : 'Novo Produto' }}</h2>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div>
          <label>Nome</label><br>
          <input v-model="form.nome" style="width: 100%; padding: 8px;" />
        </div>

        <div>
          <label>Código</label><br>
          <input v-model="form.codigo" style="width: 100%; padding: 8px;" />
        </div>

        <div>
          <label>Preço</label><br>
          <input v-model.number="form.preco" type="number" step="0.01" style="width: 100%; padding: 8px;" />
        </div>

        <div>
          <label>Quantidade</label><br>
          <input v-model.number="form.quantidade" type="number" style="width: 100%; padding: 8px;" />
        </div>

        <div>
          <label>Estoque Mínimo</label><br>
          <input v-model.number="form.estoque_minimo" type="number" style="width: 100%; padding: 8px;" />
        </div>

        <div>
          <label>Categoria</label><br>
          <select v-model="form.categoria_id" style="width: 100%; padding: 8px;">
            <option value="">Selecione...</option>
            <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
              {{ cat.nome }}
            </option>
          </select>
        </div>
      </div>

      <div style="margin-top: 15px;">
        <label>Descrição</label><br>
        <textarea v-model="form.descricao" style="width: 100%; padding: 8px; height: 60px;"></textarea>
      </div>

      <div style="margin-top: 15px;">
        <button @click="salvar" style="padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer;">
          {{ editando ? 'Atualizar' : 'Salvar' }}
        </button>
        <button v-if="editando" @click="limparFormulario" style="padding: 10px 20px; margin-left: 10px;">
          Cancelar
        </button>
      </div>
    </div>

    <!-- Lista de produtos -->
    <div v-if="carregando">Carregando...</div>
    <div v-else-if="erro" style="color: red;">{{ erro }}</div>

    <table v-else style="width: 100%; border-collapse: collapse;">
      <thead>
        <tr style="background: #333; color: white;">
          <th style="padding: 10px; text-align: left;">Nome</th>
          <th style="padding: 10px;">Código</th>
          <th style="padding: 10px;">Preço</th>
          <th style="padding: 10px;">Qtd</th>
          <th style="padding: 10px;">Categoria</th>
          <th style="padding: 10px;">Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="produto in produtos" :key="produto.id" style="border-bottom: 1px solid #ddd;">
          <td style="padding: 10px;">{{ produto.nome }}</td>
          <td style="padding: 10px; text-align: center;">{{ produto.codigo }}</td>
          <td style="padding: 10px; text-align: center;">R$ {{ produto.preco }}</td>
          <td style="padding: 10px; text-align: center;">{{ produto.quantidade }}</td>
          <td style="padding: 10px; text-align: center;">{{ produto.categoria?.nome }}</td>
          <td style="padding: 10px; text-align: center;">
            <button @click="editar(produto)" style="margin-right: 5px;">Editar</button>
            <button @click="excluir(produto.id)" style="background: #f44336; color: white; border: none; padding: 5px 10px;">
              Excluir
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <p v-if="!carregando && produtos.length === 0" style="margin-top: 20px; color: #666;">
      Nenhum produto cadastrado ainda.
    </p>
  </div>
</template>