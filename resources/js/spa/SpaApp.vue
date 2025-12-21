<template>
    <div>
        <!-- Navbar (Bootstrap) -->
        <nav v-if="!isAuthRoute" class="navbar navbar-expand-lg navbar-light app-navbar border-bottom">
            <div class="container">
                <RouterLink class="navbar-brand d-flex align-items-center" to="/">
                    <img :src="logoUrl" alt="Logo" height="40" class="me-2" />
                </RouterLink>

                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarNav"
                    aria-controls="navbarNav"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <form class="d-flex flex-grow-1 mx-lg-4 my-3 my-lg-0" role="search" @submit.prevent="onSearchSubmit">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input
                                v-model="searchQuery"
                                type="search"
                                class="form-control"
                                placeholder="Buscar projetos, criadores e categorias"
                                aria-label="Buscar"
                            />
                        </div>
                    </form>

                    <ul class="navbar-nav ms-lg-auto align-items-lg-center">
                        <template v-if="user">
                            <li class="nav-item">
                                <RouterLink class="nav-link" to="/me/campaigns/create">Para criadores</RouterLink>
                            </li>
                            <li class="nav-item dropdown">
                                <a
                                    class="nav-link dropdown-toggle"
                                    href="#"
                                    role="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                >
                                    {{ user.name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <RouterLink class="dropdown-item" to="/dashboard">
                                            <i class="bi bi-speedometer2"></i> Meu Dashboard
                                        </RouterLink>
                                    </li>
                                    <li>
                                        <RouterLink class="dropdown-item" to="/profile">Perfil</RouterLink>
                                    </li>
                                    <li><hr class="dropdown-divider" /></li>
                                    <li>
                                        <button type="button" class="dropdown-item" @click="logout">Sair</button>
                                    </li>
                                </ul>
                            </li>
                        </template>

                        <template v-else>
                            <li class="nav-item">
                                <RouterLink class="nav-link" to="/me/campaigns/create">Para criadores</RouterLink>
                            </li>
                            <li class="nav-item">
                                <RouterLink class="nav-link" to="/login">Entrar</RouterLink>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Categories bar -->
        <div v-if="!isAuthRoute" class="border-bottom bg-white app-categories">
            <div class="container">
                <nav class="nav flex-nowrap overflow-auto py-2" aria-label="Categorias">
                    <RouterLink
                        v-for="cat in categories"
                        :key="cat.key"
                        class="nav-link text-nowrap"
                        :to="{ path: '/campaigns', query: { category: cat.key } }"
                    >
                        {{ cat.label }}
                    </RouterLink>
                </nav>
            </div>
        </div>

        <!-- Flash Messages -->
        <div v-if="flashSuccess" class="alert alert-success alert-dismissible fade show m-0" role="alert">
            <div class="container">
                {{ flashSuccess }}
                <button type="button" class="btn-close" aria-label="Close" @click="flashSuccess = ''"></button>
            </div>
        </div>
        <div v-if="flashError" class="alert alert-danger alert-dismissible fade show m-0" role="alert">
            <div class="container">
                {{ flashError }}
                <button type="button" class="btn-close" aria-label="Close" @click="flashError = ''"></button>
            </div>
        </div>

        <!-- Main Content -->
        <main :class="isAuthRoute ? 'py-0' : 'py-4'">
            <div v-if="loading" class="container text-muted">Carregando…</div>
            <RouterView
                v-else
                :key="$route.fullPath"
                :user="user"
                @auth-updated="fetchMe"
                @flash-success="setFlashSuccess"
                @flash-error="setFlashError"
            />
        </main>

        <!-- Footer -->
        <footer v-if="!isAuthRoute" class="py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Catarse</h5>
                        <p class="text-muted">Plataforma de crowdfunding para projetos criativos.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="text-muted mb-0">&copy; {{ year }} Catarse. Todos os direitos reservados.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { apiGet, apiPost } from './api';

const router = useRouter();
const route = useRoute();

const user = ref(null);
const loading = ref(true);
const flashSuccess = ref('');
const flashError = ref('');

const year = computed(() => new Date().getFullYear());

const isAuthRoute = computed(() => route.name === 'login' || route.name === 'register');

const logoUrl = '/img/logo.svg';

const searchQuery = ref('');

const categories = [
    { key: 'arte', label: 'Arte' },
    { key: 'quadrinhos', label: 'Quadrinhos' },
    { key: 'artesanato', label: 'Artesanato' },
    { key: 'danca', label: 'Dança' },
    { key: 'design', label: 'Design' },
    { key: 'moda', label: 'Moda' },
    { key: 'filme', label: 'Filme' },
    { key: 'comida', label: 'Comida' },
    { key: 'jogos', label: 'Jogos' },
    { key: 'jornalismo', label: 'Jornalismo' },
    { key: 'musica', label: 'Música' },
    { key: 'fotografia', label: 'Fotografia' },
    { key: 'publicacao', label: 'Publicação' },
    { key: 'tecnologia', label: 'Tecnologia' },
    { key: 'teatro', label: 'Teatro' },
];

function setFlashSuccess(message) {
    flashSuccess.value = message || '';
    flashError.value = '';
}

function setFlashError(message) {
    flashError.value = message || '';
    flashSuccess.value = '';
}

async function fetchMe() {
    try {
        const data = await apiGet('/api/me');
        user.value = data.user;
    } catch {
        user.value = null;
    }
}

async function logout() {
    try {
        await apiPost('/api/logout', {});
        setFlashSuccess('Você saiu da sua conta.');
    } catch {
        setFlashError('Erro ao sair.');
    } finally {
        await fetchMe();
        router.push('/');
    }
}

function onSearchSubmit() {
    const q = String(searchQuery.value || '').trim();
    router.push({ path: '/campaigns', query: q ? { q } : {} });
}

onMounted(async () => {
    await fetchMe();
    loading.value = false;
});
</script>
