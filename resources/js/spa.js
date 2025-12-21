import './bootstrap';

import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';

import SpaApp from './spa/SpaApp.vue';
import { routes } from './spa/routes';
import { applyRouteSeo } from './spa/seo';
import { i18n } from './spa/i18n';

window.axios.defaults.withCredentials = true;
window.axios.defaults.headers.common['Accept'] = 'application/json';

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior() {
        return { top: 0 };
    },
});

router.afterEach((to) => {
    applyRouteSeo(to);
});

createApp(SpaApp).use(router).use(i18n).mount('#app');
