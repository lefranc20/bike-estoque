import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import ProdutosView from '../views/ProdutosView.vue'
import MovimentacoesView from '../views/MovimentacoesView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/produtos',
      name: 'produtos',
      component: ProdutosView
    },
    {
      path: '/movimentacoes',
      name: 'movimentacoes',
      component: MovimentacoesView
    }
  ]
})

export default router