import { createRouter, createWebHistory } from 'vue-router';
import Transactions from '@/components/app/transactions/TransactionForm.vue';
import Dashboard from '@/components/app/dashboard/index.vue';

const routes = [
  { path: '/', redirect: '/app/dashboard' },
  { path: '/app', redirect: '/app/dashboard'},
  { path: '/app/dashboard', component: Dashboard },
  { path: '/app/transactions', component: Transactions },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
