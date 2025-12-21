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
                                <RouterLink class="nav-link" to="/me/creator/setup">Para criadores</RouterLink>
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
                                <RouterLink class="nav-link" to="/me/creator/setup">Para criadores</RouterLink>
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
        <footer v-if="!isAuthRoute" class="bg-dark text-light pt-5 pb-4 mt-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-6 col-md-3">
                        <h6 class="fw-semibold">Bem-vindo</h6>
                        <ul class="list-unstyled mt-3 mb-0">
                            <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/about">Quem Somos</RouterLink></li>
                            <li class="mb-2">
                                <RouterLink class="link-light text-decoration-none" to="/how-it-works">Como funciona</RouterLink>
                            </li>
                            <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/blog">Blog</RouterLink></li>
                            <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/team">Nosso time</RouterLink></li>
                            <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/press">Imprensa</RouterLink></li>
                            <li class="mb-2">
                                <RouterLink class="link-light text-decoration-none" to="/retrospectiva-2020">Retrospectiva 2020</RouterLink>
                            </li>
                        </ul>

                        <div class="mt-4">
                            <h6 class="fw-semibold">Redes Sociais</h6>
                            <ul class="list-unstyled mt-3 mb-0">
                                <li class="mb-2">
                                    <a class="link-light text-decoration-none" href="https://facebook.com" target="_blank" rel="noopener">
                                        <i class="bi bi-facebook me-2"></i>Facebook
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a class="link-light text-decoration-none" href="https://twitter.com" target="_blank" rel="noopener">
                                        <i class="bi bi-twitter-x me-2"></i>Twitter
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a class="link-light text-decoration-none" href="https://instagram.com" target="_blank" rel="noopener">
                                        <i class="bi bi-instagram me-2"></i>Instagram
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a class="link-light text-decoration-none" href="https://github.com" target="_blank" rel="noopener">
                                        <i class="bi bi-github me-2"></i>Github
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <h6 class="fw-semibold">Ajuda</h6>
                        <ul class="list-unstyled mt-3 mb-0">
                            <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/support">Central de Suporte</RouterLink></li>
                            <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/contact">Contato</RouterLink></li>
                            <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/updates">Atualizações</RouterLink></li>
                            <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/fees">Nossa Taxa</RouterLink></li>
                            <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/security">Responsabilidades e Segurança</RouterLink></li>
                            <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/terms">Termos de uso</RouterLink></li>
                            <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/privacy">Política de privacidade</RouterLink></li>
                        </ul>
                    </div>

                    <div class="col-6 col-md-3">
                        <h6 class="fw-semibold">Faça uma campanha</h6>
                        <ul class="list-unstyled mt-3 mb-0">
                            <li class="mb-2">
                                <RouterLink class="link-light text-decoration-none" to="/me/creator/setup">Comece seu projeto</RouterLink>
                            </li>
                            <li class="mb-2">
                                <RouterLink class="link-light text-decoration-none" :to="{ path: '/campaigns', query: { category: 'musica' } }">
                                    Música no Origo
                                </RouterLink>
                            </li>
                            <li class="mb-2">
                                <RouterLink
                                    class="link-light text-decoration-none"
                                    :to="{ path: '/campaigns', query: { category: 'publicacao' } }"
                                >
                                    Publicações Independentes
                                </RouterLink>
                            </li>
                            <li class="mb-2">
                                <RouterLink class="link-light text-decoration-none" :to="{ path: '/campaigns', query: { category: 'jornalismo' } }">
                                    Jornalismo
                                </RouterLink>
                            </li>
                            <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/assinaturas">Assinaturas</RouterLink></li>
                        </ul>

                        <div class="mt-4">
                            <h6 class="fw-semibold">Apoie projetos no Origo</h6>
                            <ul class="list-unstyled mt-3 mb-0">
                                <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/campaigns">Explore projetos</RouterLink></li>
                                <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/popular">Populares</RouterLink></li>
                                <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/no-ar">No ar</RouterLink></li>
                                <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/finalizados">Finalizados</RouterLink></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <h6 class="fw-semibold">Assine nossa news</h6>
                        <form class="mt-3" @submit.prevent="subscribeNewsletter">
                            <div class="input-group">
                                <input
                                    v-model="newsletterEmail"
                                    type="email"
                                    class="form-control"
                                    placeholder="Digite seu email"
                                    aria-label="Digite seu email"
                                    autocomplete="email"
                                />
                                <button class="btn btn-primary" type="submit" aria-label="Assinar">
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                            <div class="form-text text-secondary mt-2">Você pode cancelar a qualquer momento.</div>
                        </form>
                    </div>
                </div>

                <div class="border-top border-secondary mt-4 pt-4 d-flex flex-column flex-md-row justify-content-between gap-2">
                    <div class="text-secondary small">Origo — plataforma de crowdfunding para projetos criativos.</div>
                    <div class="text-secondary small">&copy; {{ year }} Origo. Todos os direitos reservados.</div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { apiGet, apiPost } from './api';
import { categories } from './categories';

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

const newsletterEmail = ref('');

// `categories` imported from ./categories

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

function subscribeNewsletter() {
    const email = String(newsletterEmail.value || '').trim();
    if (!email) {
        setFlashError('Digite um email para assinar.');
        return;
    }

    newsletterEmail.value = '';
    setFlashSuccess('Obrigado! Você foi inscrito na newsletter.');
}

onMounted(async () => {
    await fetchMe();
    loading.value = false;
});
</script>
