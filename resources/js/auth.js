import { createApp } from 'vue';
import Auth from './auth.vue';
import router from './router/auth';

createApp(Auth).use(router).mount('#auth');