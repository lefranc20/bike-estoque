import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import ProductList from '../ProductList.vue'

function criarProduto(overrides = {}) {
  return {
    id: 1,
    nome: 'Pneu Aro 29',
    codigo: 'PNEU-001',
    preco: 100,
    quantidade: 10,
    categoria: { nome: 'Rodas e Pneus' },
    esta_abaixo_do_minimo: false,
    ...overrides
  }
}

describe('ProductList', () => {
  it('mostra o aviso "Abaixo do mínimo" quando o produto está com estoque baixo', () => {
    const wrapper = mount(ProductList, {
      props: {
        aberto: true,
        produtos: [criarProduto({ esta_abaixo_do_minimo: true })]
      }
    })

    expect(wrapper.text()).toContain('Abaixo do mínimo')
  })

  it('não mostra o aviso quando o estoque está normal', () => {
    const wrapper = mount(ProductList, {
      props: {
        aberto: true,
        produtos: [criarProduto({ esta_abaixo_do_minimo: false })]
      }
    })

    expect(wrapper.text()).not.toContain('Abaixo do mínimo')
  })

  it('mostra o botão Excluir apenas para admin', () => {
    const admin = mount(ProductList, {
      props: { aberto: true, produtos: [criarProduto()], isAdmin: true }
    })
    const padrao = mount(ProductList, {
      props: { aberto: true, produtos: [criarProduto()], isAdmin: false }
    })

    expect(admin.find('button.button-secondary').exists()).toBe(true)
    expect(padrao.find('button.button-secondary').exists()).toBe(false)
    expect(padrao.text()).toContain('—')
  })

  it('emite delete com o id do produto ao clicar em Excluir', async () => {
    const wrapper = mount(ProductList, {
      props: { aberto: true, produtos: [criarProduto({ id: 42 })], isAdmin: true }
    })

    await wrapper.find('button.button-secondary').trigger('click')

    expect(wrapper.emitted('delete')[0]).toEqual([42])
  })

  it('mostra mensagem de lista vazia quando não há produtos', () => {
    const wrapper = mount(ProductList, {
      props: { aberto: true, produtos: [] }
    })

    expect(wrapper.text()).toContain('Nenhum produto cadastrado ainda.')
  })
})