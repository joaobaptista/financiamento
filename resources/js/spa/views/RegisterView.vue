<template>
    <div class="bg-light min-vh-100 d-flex flex-column">
        <div class="container py-5">
            <div class="text-center mb-2">
                <RouterLink to="/" class="d-inline-block">
                    <img :src="logoUrl" alt="Logo" style="height: 44px" />
                </RouterLink>
            </div>

            <div class="text-center mb-4">
                <h1 class="h3 fw-semibold mb-0">{{ t('auth.register.title') }}</h1>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <form @submit.prevent="submit">
                                <div class="mb-3">
                                    <label class="form-label">{{ t('auth.common.name') }}</label>
                                    <input v-model="name" type="text" class="form-control" autocomplete="name" required />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">{{ t('auth.common.email') }}</label>
                                    <input v-model="email" type="email" class="form-control" autocomplete="email" required />
                                </div>

                                <div class="mb-2">
                                    <label class="form-label mb-1">Senha</label>
                                    <div class="input-group">
                                        <input
                                            v-model="password"
                                            :type="showPassword ? 'text' : 'password'"
                                            class="form-control"
                                            autocomplete="new-password"
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

                                <div class="form-check my-3">
                                    <input id="newsletter" v-model="wantsNews" class="form-check-input" type="checkbox" />
                                    <label class="form-check-label" for="newsletter">
                                        {{ t('auth.register.newsletterOptIn') }}
                                    </label>
                                </div>

                                <div class="border rounded bg-white p-3 mb-3" style="max-width: 320px">
                                    <div class="d-flex align-items-center">
                                        <div class="form-check mb-0">
                                            <input id="robot" v-model="notRobot" class="form-check-input" type="checkbox" />
                                            <label class="form-check-label" for="robot">{{ t('auth.register.notRobot') }}</label>
                                        </div>
                                        <div class="ms-auto text-muted small">reCAPTCHA</div>
                                    </div>
                                    <div class="text-muted" style="font-size: 12px">{{ t('auth.register.recaptchaMini') }}</div>
                                </div>

                                <button type="submit" class="btn btn-success w-100 py-2" :disabled="submitting">
                                    {{ submitting ? t('auth.register.submitting') : t('auth.register.submit') }}
                                </button>

                                <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="text-muted">{{ t('auth.register.haveAccount') }}</div>
                                    <RouterLink to="/login" class="link-primary">{{ t('auth.register.signIn') }}</RouterLink>
                                </div>

                                <div v-if="error" class="alert alert-danger mt-3 mb-0" role="alert">
                                    {{ error }}
                                </div>

                                <div class="text-muted small mt-3">
                                    {{ t('auth.register.termsPrefix') }}
                                    <a href="#" class="link-primary" @click.prevent>{{ t('auth.register.termsLink') }}</a>
                                    {{ t('auth.register.and') }}
                                    <a href="#" class="link-primary" @click.prevent>{{ t('auth.register.privacyLink') }}</a>
                                </div>
                            </form>
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

const name = ref('');
const email = ref('');
const password = ref('');
const showPassword = ref(false);
const wantsNews = ref(false);
const notRobot = ref(false);
const error = ref('');
const submitting = ref(false);

const { t } = useI18n({ useScope: 'global' });

async function submit() {
    error.value = '';
    submitting.value = true;

    try {
        await apiPost('/api/register', {
            name: name.value,
            email: email.value,
            password: password.value,
            password_confirmation: password.value,
        });
        emit('auth-updated');
        router.push('/dashboard');
    } catch (e) {
        error.value = e?.response?.data?.message ?? t('auth.register.error');
    } finally {
        submitting.value = false;
    }
}
</script>
