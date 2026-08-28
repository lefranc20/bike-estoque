import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

export const useTemaStore = defineStore('tema', () => {
  const tema = ref(document.documentElement.getAttribute('data-theme') || 'dark')

  function alternar() {
    tema.value = tema.value === 'dark' ? 'light' : 'dark'
  }

  watch(tema, (novoTema) => {
    document.documentElement.setAttribute('data-theme', novoTema)
    localStorage.setItem('tema', novoTema)
  })

  return { tema, alternar }
})
