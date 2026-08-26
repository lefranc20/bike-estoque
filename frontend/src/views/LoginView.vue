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

const mouseX = ref(0.5)
const mouseY = ref(0.5)

function onMouseMove(evento) {
  const rect = evento.currentTarget.getBoundingClientRect()
  mouseX.value = (evento.clientX - rect.left) / rect.width
  mouseY.value = (evento.clientY - rect.top) / rect.height
}

function onMouseLeave() {
  mouseX.value = 0.5
  mouseY.value = 0.5
}

function parallax(profundidade) {
  const dx = (mouseX.value - 0.5) * 36 * profundidade
  const dy = (mouseY.value - 0.5) * 36 * profundidade
  return { transform: `translate(${dx}px, ${dy}px)` }
}

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
    <div class="login-shell">
      <div class="login-visual" @mousemove="onMouseMove" @mouseleave="onMouseLeave">
        <div class="blob-wrap" :style="parallax(0.6)">
          <div class="blob blob-a"></div>
        </div>
        <div class="blob-wrap" :style="parallax(1)">
          <div class="blob blob-b"></div>
        </div>
        <div class="blob-wrap" :style="parallax(1.5)">
          <div class="blob blob-c"></div>
        </div>

        <div class="login-visual-content">
          <span class="login-visual-emoji">🚲</span>
          <h1 class="login-visual-title">Bike Estoque</h1>
          <p class="login-visual-subtitle">
            Controle de produtos, categorias e movimentações de estoque num só lugar.
          </p>
        </div>
      </div>

      <form class="login-form-panel" @submit.prevent="entrar">
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

        <h2 class="login-title">Entrar</h2>
        <p class="login-subtitle">Acesse sua conta para continuar.</p>

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

.login-shell {
  width: min(100%, 920px);
  display: grid;
  grid-template-columns: 1fr 1fr;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 22px 48px rgba(15, 23, 42, 0.34);
  border: 1px solid rgba(148, 163, 184, 0.12);
}

.login-visual {
  position: relative;
  overflow: hidden;
  min-height: 460px;
  background: linear-gradient(165deg, #0d1c36 0%, #08111f 65%);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: default;
}

.blob-wrap {
  position: absolute;
  inset: 0;
  pointer-events: none;
  transition: transform 0.35s ease-out;
}

.blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(60px);
  opacity: 0.55;
  animation: float ease-in-out infinite;
}

.blob-a {
  width: 280px;
  height: 280px;
  top: -60px;
  left: -50px;
  background: #3b82f6;
  animation-duration: 10s;
}

.blob-b {
  width: 240px;
  height: 240px;
  bottom: -50px;
  right: -40px;
  background: #0ea5e9;
  animation-duration: 12s;
  animation-delay: -4s;
}

.blob-c {
  width: 200px;
  height: 200px;
  top: 42%;
  left: 32%;
  background: #22d3ee;
  animation-duration: 14s;
  animation-delay: -7s;
}

@keyframes float {
  0%, 100% {
    transform: translate(0, 0) scale(1);
  }
  50% {
    transform: translate(18px, -14px) scale(1.08);
  }
}

.login-visual-content {
  position: relative;
  z-index: 1;
  text-align: center;
  padding: 2.5rem;
}

.login-visual-emoji {
  display: inline-block;
  font-size: 2.75rem;
  margin-bottom: 0.75rem;
}

.login-visual-title {
  margin: 0 0 0.6rem;
  color: #f8fafc;
  font-size: 1.9rem;
  font-weight: 700;
}

.login-visual-subtitle {
  margin: 0 auto;
  max-width: 320px;
  color: #cbd5e1;
  line-height: 1.6;
}

.login-form-panel {
  background: rgba(15, 23, 42, 0.92);
  padding: 2.75rem 2.5rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.login-badge {
  align-self: flex-start;
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

@media (max-width: 860px) {
  .login-shell {
    grid-template-columns: 1fr;
  }

  .login-visual {
    min-height: 220px;
  }
}
</style>
