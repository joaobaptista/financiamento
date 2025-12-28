import './bootstrap';

import Alpine from '@alpinejs/csp';
import { createApp } from 'vue';
import App from './vue/App.vue';

window.Alpine = Alpine;

Alpine.start();

const vueRoot = document.getElementById('vue-app');

if (vueRoot) {
	createApp(App).mount(vueRoot);
}
