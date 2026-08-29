import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import Pager from '../Pager.vue'

describe('Pager', () => {
  it('não renderiza nada quando só existe uma página', () => {
    const wrapper = mount(Pager, {
      props: { paginaAtual: 1, totalPaginas: 1, total: 3 }
    })

    expect(wrapper.find('.pager').exists()).toBe(false)
  })

  it('mostra a página atual, o total de páginas e o total de itens', () => {
    const wrapper = mount(Pager, {
      props: { paginaAtual: 2, totalPaginas: 4, total: 37 }
    })

    expect(wrapper.text()).toContain('Página 2 de 4')
    expect(wrapper.text()).toContain('37 itens')
  })

  it('desabilita "Anterior" na primeira página e "Próxima" na última', () => {
    const wrapper = mount(Pager, {
      props: { paginaAtual: 1, totalPaginas: 3, total: 10 }
    })
    const [anterior, proxima] = wrapper.findAll('button')

    expect(anterior.attributes('disabled')).toBeDefined()
    expect(proxima.attributes('disabled')).toBeUndefined()
  })

  it('emite mudar-pagina com o número correto ao clicar em Próxima e Anterior', async () => {
    const wrapper = mount(Pager, {
      props: { paginaAtual: 2, totalPaginas: 3, total: 10 }
    })
    const [anterior, proxima] = wrapper.findAll('button')

    await proxima.trigger('click')
    await anterior.trigger('click')

    expect(wrapper.emitted('mudar-pagina')[0]).toEqual([3])
    expect(wrapper.emitted('mudar-pagina')[1]).toEqual([1])
  })
})