<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useTemaStore } from '../stores/tema'

const auth = useAuthStore()
const tema = useTemaStore()
const router = useRouter()

async function sair() {
  await auth.logout()
  router.push('/login')
}

defineProps({
  eyebrow: {
    type: String,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    required: true
  }
})

const dispositivo = ref('Desktop')
const mobileQuery = window.matchMedia('(max-width: 760px)')

function atualizarDispositivo(evento) {
  dispositivo.value = evento.matches ? 'Mobile' : 'Desktop'
}

onMounted(() => {
  atualizarDispositivo(mobileQuery)
  mobileQuery.addEventListener('change', atualizarDispositivo)
})

onUnmounted(() => {
  mobileQuery.removeEventListener('change', atualizarDispositivo)
})
</script>

<template>
  <div class="flex justify-between items-start gap-6 mb-10 max-[760px]:flex-col max-[760px]:items-stretch">
    <div class="max-w-[720px]">
      <p class="inline-flex items-center gap-[0.6rem] mb-[0.8rem] text-[0.72rem] tracking-[0.08em] uppercase text-[color:var(--cor-link)] opacity-90">
        <span>{{ eyebrow }}</span>
        <span class="opacity-50">•</span>
        <span>{{ dispositivo }}</span>
        <template v-if="auth.user">
          <span class="opacity-50">•</span>
          <span>{{ auth.user.name }}</span>
        </template>
      </p>
      <h1 class="font-bold text-[clamp(2.3rem,3vw,3rem)] text-[color:var(--cor-texto-forte)] leading-[1.1]">
        {{ title }}
      </h1>
      <p class="mt-[0.8rem] text-[color:var(--cor-texto-medio)] text-[1.05rem] leading-[1.7] max-w-[720px]">
        {{ subtitle }}
      </p>
    </div>
    <nav
      class="dashboard-actions flex items-center gap-[0.9rem] flex-wrap shrink-0 pt-2 max-[760px]:justify-start"
      aria-label="Navegação principal"
    >
      <RouterLink to="/">Dashboard</RouterLink>
      <RouterLink to="/produtos">Produtos</RouterLink>
      <RouterLink to="/movimentacoes">Movimentações</RouterLink>
      <RouterLink to="/relatorios">Relatórios</RouterLink>
      <button
        class="inline-flex items-center justify-center w-11 h-11 min-w-[auto] px-[1.2rem] py-[0.8rem]
          rounded-full bg-[var(--cor-chip-fundo)] border border-[color:var(--cor-borda-forte)]
          text-[color:var(--cor-texto)] text-[1.2rem] cursor-pointer transition-all duration-200
          hover:bg-[rgba(59,130,246,0.12)] hover:border-[rgba(96,165,250,0.35)] hover:-translate-y-px"
        type="button"
        :aria-label="tema.tema === 'dark' ? 'Ativar tema claro' : 'Ativar tema escuro'"
        :title="tema.tema === 'dark' ? 'Ativar tema claro' : 'Ativar tema escuro'"
        @click="tema.alternar()"
      >
        {{ tema.tema === 'dark' ? '☀️' : '🌙' }}
      </button>
      <button
        class="min-w-[auto] px-[1.2rem] py-[0.8rem] rounded-full bg-[rgba(239,68,68,0.12)]
          border border-[rgba(248,113,113,0.4)] text-[color:var(--cor-erro-texto)] font-[inherit]
          text-[0.9375rem] leading-6 font-semibold cursor-pointer transition-all duration-200
          hover:bg-[rgba(239,68,68,0.22)] hover:border-[rgba(248,113,113,0.6)]"
        type="button"
        @click="sair"
      >
        Sair
      </button>
    </nav>
  </div>
</template>

<style scoped>
/* @reference dá ao @apply acesso ao Tailwind sem gerar o framework inteiro de novo
   aqui dentro — necessário porque <style scoped> do Vue é tratado como um CSS isolado,
   diferente do main.css (que já importa 'tailwindcss' no topo dele mesmo). */
@reference 'tailwindcss';

/* Só os links de navegação ficam aqui: são 4 instâncias idênticas, então uma classe
   com @apply evita repetir a mesma string enorme de utilitárias 4 vezes no template. */
.dashboard-actions a {
  @apply inline-flex items-center justify-center min-w-[120px] px-[1.2rem] py-[0.8rem] rounded-full
    bg-[var(--cor-chip-fundo)] border border-[color:var(--cor-borda-forte)] text-[color:var(--cor-texto)]
    no-underline font-semibold transition-all duration-200;
}

.dashboard-actions a:hover {
  @apply bg-[rgba(59,130,246,0.12)] border-[rgba(96,165,250,0.35)];
}

.dashboard-actions a.router-link-exact-active {
  @apply bg-[rgba(37,99,235,0.24)] border-[rgba(96,165,250,0.5)] text-[color:var(--cor-link)];
}
</style>
