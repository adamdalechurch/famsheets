import { createRouter, createWebHistory } from 'vue-router';
import SignIn from '../components/auth/SignIn.vue';
import SignUp from '../components/auth/SignUp.vue';
import PasswordReset from '../components/auth/PasswordReset.vue';

const routes = [
  { path: '/', redirect: '/auth/signin' },
  { path: '/auth/signin', component: SignIn },
  { path: '/auth/signup', component: SignUp },
  { path: '/auth/password-reset', component: PasswordReset },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
