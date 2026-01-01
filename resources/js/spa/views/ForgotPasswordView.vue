<template>
    <div class="bg-light min-vh-100 d-flex flex-column">
        <div class="container py-5">
            <div class="text-center mb-4">
                <RouterLink to="/" class="d-inline-block">
                    <img :src="logoUrl" alt="Logo" style="height: 28px" />
                </RouterLink>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-7 col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <h1 class="h3 fw-normal mb-2">{{ t('auth.forgotPassword.title') }}</h1>
                            <p class="text-muted mb-4">{{ t('auth.forgotPassword.subtitle') }}</p>

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

                                    <div v-if="fieldError" class="invalid-feedback d-block">{{ fieldError }}</div>
                                </div>

                                <button type="submit" class="btn btn-success w-100 py-2" :disabled="submitting">
                                    {{ submitting ? t('auth.forgotPassword.submitting') : t('auth.forgotPassword.submit') }}
                                </button>

                                <div v-if="success" class="alert alert-success mt-3 mb-0" role="alert">
                                    {{ success }}
                                </div>

                                <div v-if="error" class="alert alert-danger mt-3 mb-0" role="alert">
                                    {{ error }}
                                </div>
                            </form>
                        </div>

                        <div class="border-top p-3 text-center">
                            <RouterLink to="/login" class="link-primary">{{ t('auth.forgotPassword.backToLogin') }}</RouterLink>
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
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { apiPost } from '../api';

const logoUrl = '/img/logo.svg';

const { t } = useI18n({ useScope: 'global' });

const email = ref('');
const submitting = ref(false);
const success = ref('');
const error = ref('');
const errors = ref({});

const fieldError = computed(() => {
    const msg = errors.value?.email;
    if (Array.isArray(msg)) return msg[0];
    if (typeof msg === 'string') return msg;
    return '';
});

async function submit() {
    success.value = '';
    error.value = '';
    errors.value = {};
    submitting.value = true;

    try {
        const data = await apiPost('/forgot-password', { email: email.value });
        success.value = data?.status ?? t('auth.forgotPassword.success');
    } catch (e) {
        errors.value = e?.response?.data?.errors ?? {};
        error.value = e?.response?.data?.message ?? t('auth.forgotPassword.error');
    } finally {
        submitting.value = false;
    }
}
</script>
