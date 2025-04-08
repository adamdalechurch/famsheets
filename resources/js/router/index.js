import { createRouter, createWebHistory } from 'vue-router';
import SignIn from '../components/auth/SignIn.vue';
import SignUp from '../components/auth/SignUp.vue';
import PasswordReset from '../components/auth/PasswordReset.vue';
import Transactions from '../components/app/TransactionForm.vue';

const routes = [
  { path: '/', redirect: '/signin' },
  { path: '/signin', component: SignIn },
  { path: '/signup', component: SignUp },
  { path: '/password-reset', component: PasswordReset },
  { path: '/transactions', component: Transactions },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
