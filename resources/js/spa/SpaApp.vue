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
                                :placeholder="t('navbar.searchPlaceholder')"
                                :aria-label="t('navbar.searchAria')"
                            />
                        </div>
                    </form>

                    <ul class="navbar-nav ms-lg-auto align-items-lg-center">
                        <template v-if="user">
                            <li class="nav-item dropdown">
                                <a
                                    class="nav-link dropdown-toggle"
                                    href="#"
                                    role="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                >
                                    <img
                                        v-if="user.profile_photo_url"
                                        :src="user.profile_photo_url"
                                        alt=""
                                        class="rounded-circle border me-2"
                                        style="width: 24px; height: 24px; object-fit: cover"
                                    />
                                    {{ user.name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <RouterLink class="dropdown-item" to="/dashboard">
                                            <i class="bi bi-speedometer2"></i> {{ t('navbar.dashboard') }}
                                        </RouterLink>
                                    </li>
                                    <li>
                                        <RouterLink class="dropdown-item" to="/profile">{{ t('navbar.profile') }}</RouterLink>
                                    </li>
                                    <li><hr class="dropdown-divider" /></li>
                                    <li>
                                        <button type="button" class="dropdown-item" @click="logout">{{ t('navbar.logout') }}</button>
                                    </li>
                                </ul>
                            </li>
                        </template>

                        <template v-else>
                            <li class="nav-item">
                                <RouterLink class="nav-link" to="/register">{{ t('navbar.register') }}</RouterLink>
                            </li>
                            <li class="nav-item">
                                <RouterLink class="nav-link" to="/login">{{ t('navbar.login') }}</RouterLink>
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
                        {{ t(cat.labelKey) }}
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
        <main :class="isAuthRoute ? 'py-0' : 'pt-2 pb-4'">
            <div v-if="loading" class="container text-muted">{{ t('common.loading') }}</div>
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
                        <h6 class="fw-semibold origo-footer-heading">{{ t('footer.welcome') }}</h6>
                        <ul class="list-unstyled mt-3 mb-0">
                            <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/blog">{{ t('footer.blog') }}</RouterLink></li>
                        </ul>

                        <div class="mt-4">
                            <h6 class="fw-semibold origo-footer-heading">{{ t('footer.socialTitle') }}</h6>
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
                            </ul>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <h6 class="fw-semibold origo-footer-heading">{{ t('footer.help') }}</h6>
                        <ul class="list-unstyled mt-3 mb-0">
                            <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/terms">{{ t('footer.terms') }}</RouterLink></li>
                            <li class="mb-2"><RouterLink class="link-light text-decoration-none" to="/privacy">{{ t('footer.privacy') }}</RouterLink></li>
                        </ul>
                    </div>

                    <div class="col-6 col-md-3">
                        <h6 class="fw-semibold origo-footer-heading">{{ t('footer.makeCampaign') }}</h6>
                        <ul class="list-unstyled mt-3 mb-0">
                            <li class="mb-2">
                                <RouterLink class="link-light text-decoration-none" to="/me/creator/setup">{{ t('footer.startProject') }}</RouterLink>
                            </li>
                        </ul>
                    </div>

                    <div class="col-12 col-md-3">
                        <h6 class="fw-semibold">{{ t('footer.subscribeTitle') }}</h6>
                        <form class="mt-3" @submit.prevent="subscribeNewsletter">
                            <div class="input-group">
                                <input
                                    v-model="newsletterEmail"
                                    type="email"
                                    class="form-control"
                                    :placeholder="t('footer.emailPlaceholder')"
                                    :aria-label="t('footer.emailPlaceholder')"
                                    autocomplete="email"
                                />
                                <button class="btn btn-primary" type="submit" :aria-label="t('footer.subscribeAria')">
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                            <div class="form-text text-secondary mt-2">{{ t('footer.cancelAnytime') }}</div>
                        </form>
                    </div>
                </div>

                <div class="border-top border-secondary mt-4 pt-4 d-flex flex-column flex-md-row justify-content-between gap-2">
                    <div class="text-secondary small">&copy; {{ year }} Origo. {{ t('footer.rights') }}</div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="text-secondary small">{{ t('footer.language') }}</div>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Selecionar idioma">
                            <button
                                v-for="opt in localeOptions"
                                :key="opt.value"
                                type="button"
                                class="btn"
                                :class="currentLocale === opt.value ? 'btn-light text-dark' : 'btn-outline-light'"
                                @click="switchLocale(opt.value)"
                            >
                                {{ localeLabel(opt.value) }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { apiGet, apiPost } from './api';
import { categories } from './categories';
import { normalizeLocaleForApp } from './i18n';

const router = useRouter();
const route = useRoute();

const user = ref(null);
const loading = ref(true);
const flashSuccess = ref('');
const flashError = ref('');

const { t, locale } = useI18n({ useScope: 'global' });

const year = computed(() => new Date().getFullYear());

const isAuthRoute = computed(() =>
    route.name === 'login' ||
    route.name === 'register' ||
    route.name === 'password.request' ||
    route.name === 'password.reset'
);

const logoUrl = '/img/logo.svg';

const searchQuery = ref('');

const newsletterEmail = ref('');

const localeOptions = [
    { value: 'pt_BR' },
    { value: 'en' },
    { value: 'es' },
];

const currentLocale = ref('pt_BR');

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
        setFlashSuccess(t('flash.loggedOut'));
    } catch {
        setFlashError(t('flash.logoutError'));
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
        setFlashError(t('newsletter.missingEmail'));
        return;
    }

    newsletterEmail.value = '';
    setFlashSuccess(t('newsletter.subscribed'));
}

function localeLabel(localeValue) {
    const normalized = normalizeLocaleForApp(localeValue);
    if (normalized === 'pt_BR') return t('footer.langPortuguese');
    if (normalized === 'en') return t('footer.langEnglish');
    if (normalized === 'es') return t('footer.langSpanish');
    return normalized;
}

function switchLocale(locale) {
    const next = normalizeLocaleForApp(locale);
    const url = new URL(window.location.href);
    url.searchParams.set('lang', next);
    window.location.href = url.toString();
}

onMounted(async () => {
    currentLocale.value = normalizeLocaleForApp(document.documentElement.lang);
    locale.value = currentLocale.value;
    await fetchMe();
    loading.value = false;
});
</script>
