import './bootstrap';

import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';

import SpaApp from './spa/SpaApp.vue';
import { routes } from './spa/routes';

window.axios.defaults.withCredentials = true;
window.axios.defaults.headers.common['Accept'] = 'application/json';

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior() {
        return { top: 0 };
    },
});

createApp(SpaApp).use(router).mount('#app');
