import { createRouter, createWebHistory } from 'vue-router';
import Transactions from '@/components/app/transactions/index.vue';
import Budgets from '@/components/app/budgets/index.vue';
import Dashboard from '@/components/app/dashboard/index.vue';
import Debug from '@/components/app/debug/index.vue';
import Categories from '@/components/app/categories/index.vue';

const routes = [
  { path: '/', redirect: '/app/dashboard' },
  { path: '/app', redirect: '/app/dashboard' },
  { path: '/app/dashboard', component: Dashboard },
  // transactions:
  { path: '/app/transactions', component: Transactions },
  { path: '/app/transactions/new', component: Transactions },
  { path: '/app/transactions/:transaction_id', component: Transactions },

  // budgets:
  { path: '/app/budgets', component: Budgets },
  { path: '/app/budgets/new', component: Budgets },
  {path: '/app/budgets/:budget_id', component: Budgets },
  { path: '/app/categories', component: Categories },
  // debug:
  { path: '/app/debug', component: Debug },
  
];

const router = createRouter({
  history: createWebHistory(),
  routes,
}); //

export default router;
