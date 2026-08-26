<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()

const username = ref('')
const password = ref('')
const erro = ref(null)
const carregando = ref(false)
const mostrarInfoTeste = ref(false)

const usuariosDeTeste = [
  { papel: 'Admin', usuario: 'admin', senha: 'admin123' },
  { papel: 'Padrão', usuario: 'padrao', senha: 'padrao123' }
]

async function entrar() {
  erro.value = null
  carregando.value = true

  try {
    await auth.login(username.value, password.value)
    router.push(route.query.redirect || '/')
  } catch (e) {
    if (e.response?.status === 422) {
      erro.value = e.response.data.errors?.username?.[0] || 'Credenciais inválidas.'
    } else {
      erro.value = 'Não foi possível entrar. Tente novamente.'
    }
  } finally {
    carregando.value = false
  }
}
</script>

<template>
  <div class="login-page">
    <form class="page-panel login-panel" @submit.prevent="entrar">
      <button
        class="login-badge"
        type="button"
        :aria-expanded="mostrarInfoTeste"
        @click.stop="mostrarInfoTeste = !mostrarInfoTeste"
      >
        AVISO: Versão de testes — {{ mostrarInfoTeste ? 'ocultar usuários' : 'ver usuários' }} {{ mostrarInfoTeste ? '▲' : '▼' }}
      </button>

      <div v-if="mostrarInfoTeste" class="login-info-box">
        <p>
          Esta é uma versão de testes do Bike Estoque. Os dados cadastrados aqui podem ser apagados
          a qualquer momento.
        </p>
        <table class="login-info-table">
          <thead>
            <tr>
              <th>Papel</th>
              <th>Usuário</th>
              <th>Senha</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="u in usuariosDeTeste" :key="u.usuario">
              <td>{{ u.papel }}</td>
              <td>{{ u.usuario }}</td>
              <td>{{ u.senha }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <h1 class="login-title">Bike Estoque</h1>
      <p class="login-subtitle">Entre com sua conta pra continuar.</p>

      <div v-if="erro" class="status-inline error">{{ erro }}</div>

      <div class="form-field">
        <label for="login-username">Usuário</label>
        <input id="login-username" v-model="username" type="text" autocomplete="username" required />
      </div>

      <div class="form-field">
        <label for="login-password">Senha</label>
        <input id="login-password" v-model="password" type="password" autocomplete="current-password" required />
      </div>

      <button class="button-primary login-button" type="submit" :disabled="carregando">
        {{ carregando ? 'Entrando...' : 'Entrar' }}
      </button>
    </form>
  </div>
</template>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #08111f;
  padding: 24px;
}

.login-panel {
  width: min(100%, 420px);
  margin-bottom: 0;
}

.login-badge {
  display: inline-flex;
  margin-bottom: 1rem;
  padding: 0.3rem 0.75rem;
  border-radius: 999px;
  background: rgba(245, 158, 11, 0.14);
  border: 1px solid rgba(245, 158, 11, 0.35);
  color: #fbbf24;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  cursor: pointer;
}

.login-badge:hover {
  background: rgba(245, 158, 11, 0.22);
}

.login-info-box {
  margin-bottom: 1.5rem;
  padding: 1rem 1.1rem;
  border-radius: 14px;
  background: rgba(245, 158, 11, 0.08);
  border: 1px solid rgba(245, 158, 11, 0.25);
}

.login-info-box p {
  margin: 0 0 0.8rem;
  color: #cbd5e1;
  font-size: 0.85rem;
  line-height: 1.5;
}

.login-info-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.82rem;
}

.login-info-table th,
.login-info-table td {
  text-align: left;
  padding: 0.35rem 0.5rem;
  color: #e2e8f0;
}

.login-info-table th {
  color: #94a3b8;
  font-weight: 600;
}

.login-info-table tr + tr td {
  border-top: 1px solid rgba(148, 163, 184, 0.14);
}

.login-title {
  margin: 0 0 0.4rem;
  color: #f8fafc;
  font-size: 1.6rem;
}

.login-subtitle {
  margin: 0 0 1.5rem;
  color: #94a3b8;
}

.login-button {
  width: 100%;
  margin-top: 0.5rem;
}
</style>
