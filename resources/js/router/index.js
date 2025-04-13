import { createRouter, createWebHistory } from 'vue-router';
import Transactions from '@/components/app/transactions/index.vue';
import Dashboard from '@/components/app/dashboard/index.vue';

const routes = [
  { path: '/', redirect: '/app/dashboard' },
  { path: '/app', redirect: '/app/dashboard' },
  { path: '/app/dashboard', component: Dashboard },
  // transactions:
  { path: '/app/transactions', component: Transactions },
  { path: '/app/transactions/new', component: Transactions },
  { path: '/app/transactions/:transaction_id', component: Transactions },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
}); //

export default router;
