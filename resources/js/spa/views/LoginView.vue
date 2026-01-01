<template>
    <div class="bg-light min-vh-100 d-flex flex-column">
        <div class="container py-5">
            <div class="text-center mb-4">
                <RouterLink to="/" class="d-inline-block">
                    <img :src="logoUrl" alt="Logo" style="height: 28px" />
                </RouterLink>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-6 col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <h1 class="h3 fw-normal mb-3">{{ t('auth.login.title') }}</h1>

                            <form @submit.prevent="submit">
                                <div class="mb-3">
                                    <input
                                        v-model="email"
                                        type="email"
                                        class="form-control"
                                        :placeholder="t('auth.common.email')"
                                        autocomplete="email"
                                        required
                                    />
                                </div>

                                <div class="mb-2">
                                    <div class="input-group">
                                        <input
                                            v-model="password"
                                            :type="showPassword ? 'text' : 'password'"
                                            class="form-control"
                                            :placeholder="t('auth.common.password')"
                                            autocomplete="current-password"
                                            required
                                        />
                                        <button
                                            type="button"
                                            class="btn btn-outline-secondary"
                                            :disabled="submitting"
                                            :aria-label="showPassword ? 'Ocultar senha' : 'Mostrar senha'"
                                            @click="showPassword = !showPassword"
                                        >
                                            <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <a href="#" class="small link-primary" @click.prevent="goToForgotPassword">{{ t('auth.login.forgotPassword') }}</a>
                                </div>

                                <button type="submit" class="btn btn-success w-100 py-2" :disabled="submitting">
                                    {{ submitting ? t('auth.login.submitting') : t('auth.login.submit') }}
                                </button>

                                <div class="form-check mt-3">
                                    <input id="remember" v-model="remember" class="form-check-input" type="checkbox" />
                                    <label class="form-check-label" for="remember">{{ t('auth.login.rememberMe') }}</label>
                                </div>

                                <div v-if="error" class="alert alert-danger mt-3 mb-0" role="alert">
                                    {{ error }}
                                </div>

                            </form>
                        </div>

                        <div class="border-top p-3 text-center">
                            <span class="text-muted">{{ t('auth.login.newHere') }}</span>
                            <RouterLink to="/register" class="link-primary">{{ t('auth.login.signUp') }}</RouterLink>
                        </div>

                        <div class="border-top p-3 text-center text-muted small">
                            {{ t('auth.recaptcha.prefix') }}
                            <a href="#" class="link-primary" @click.prevent>{{ t('auth.recaptcha.privacy') }}</a>
                            {{ t('auth.recaptcha.and') }}
                            <a href="#" class="link-primary" @click.prevent>{{ t('auth.recaptcha.terms') }}</a>
                            {{ t('auth.recaptcha.suffix') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { apiPost } from '../api';

const emit = defineEmits(['auth-updated']);
const router = useRouter();

const logoUrl = '/img/logo.svg';

const email = ref('');
const password = ref('');
const showPassword = ref(false);
const remember = ref(false);
const error = ref('');
const submitting = ref(false);

const { t } = useI18n({ useScope: 'global' });

function goToForgotPassword() {
    router.push('/forgot-password');
}

async function submit() {
    error.value = '';
    submitting.value = true;

    try {
        await apiPost('/api/login', {
            email: email.value,
            password: password.value,
            remember: remember.value,
        });
        emit('auth-updated');
        router.push('/dashboard');
    } catch (e) {
        error.value = e?.response?.data?.message ?? t('auth.login.error');
    } finally {
        submitting.value = false;
    }
}
</script>
