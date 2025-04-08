import { createApp } from 'vue';
import Auth from './auth.vue';
import router from './router';

createApp(Auth).use(router).mount('#auth');