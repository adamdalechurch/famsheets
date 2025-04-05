import { createRouter, createWebHistory } from 'vue-router';

// Import Vue Components
import TransactionForm from '../components/TransactionForm.vue';
// import TransactionSchedule from '../components/TransactionSchedule.vue';
// import CategoryList from '../components/CategoryList.vue';
// import IncomeSourceList from '../components/IncomeSourceList.vue';
// import UserGroupList from '../components/UserGroupList.vue';
// import Login from '../components/Login.vue';
// import Register from '../components/Register.vue';

// Define routes
const routes = [
    { path: '/', component: TransactionForm },
    // { path: '/transactions', component: TransactionForm },
// make transactionsform active:
    // { path: '/transaction-schedules', component: TransactionSchedule },
    // { path: '/categories', component: CategoryList },
    // { path: '/income-sources', component: IncomeSourceList },
    // { path: '/user-groups', component: UserGroupList },
    // { path: '/login', component: Login },
    // { path: '/register', component: Register }
];

// Create Router
const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
