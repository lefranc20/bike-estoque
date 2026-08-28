import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

// Aplica o tema salvo antes de montar o app, pra evitar flash do tema errado
const temaSalvo = localStorage.getItem('tema')
document.documentElement.setAttribute('data-theme', temaSalvo === 'light' ? 'light' : 'dark')

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')
