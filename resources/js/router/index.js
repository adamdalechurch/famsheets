import { createRouter, createWebHistory } from 'vue-router';
import Transactions from '@/components/app/transactions/TransactionForm.vue';

const routes = [
  { path: '/', redirect: '/app/transactions' },
  { path: '/app', redirect: '/app/transactions'},
  { path: '/app/transactions', component: Transactions },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
