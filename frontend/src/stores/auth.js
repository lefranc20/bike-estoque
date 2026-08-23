import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const carregado = ref(false)

  const autenticado = computed(() => !!user.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  async function buscarUsuario() {
    try {
      const resposta = await api.get('/user')
      user.value = resposta.data
    } catch (e) {
      user.value = null
    } finally {
      carregado.value = true
    }
  }

  async function login(username, password) {
    await api.post('/login', { username, password })
    await buscarUsuario()
  }

  async function logout() {
    await api.post('/logout')
    user.value = null
  }

  return { user, carregado, autenticado, isAdmin, buscarUsuario, login, logout }
})
