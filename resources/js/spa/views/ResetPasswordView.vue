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
                            <h1 class="h3 fw-normal mb-2">{{ t('auth.resetPassword.title') }}</h1>
                            <p class="text-muted mb-4">{{ t('auth.resetPassword.subtitle') }}</p>

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
                                    <div v-if="fieldError('email')" class="invalid-feedback d-block">{{ fieldError('email') }}</div>
                                </div>

                                <div class="mb-3">
                                    <input
                                        v-model="password"
                                        type="password"
                                        class="form-control"
                                        :placeholder="t('auth.resetPassword.newPassword')"
                                        autocomplete="new-password"
                                        required
                                    />
                                    <div v-if="fieldError('password')" class="invalid-feedback d-block">{{ fieldError('password') }}</div>
                                </div>

                                <div class="mb-3">
                                    <input
                                        v-model="passwordConfirmation"
                                        type="password"
                                        class="form-control"
                                        :placeholder="t('auth.resetPassword.confirmPassword')"
                                        autocomplete="new-password"
                                        required
                                    />
                                </div>

                                <button type="submit" class="btn btn-success w-100 py-2" :disabled="submitting">
                                    {{ submitting ? t('auth.resetPassword.submitting') : t('auth.resetPassword.submit') }}
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
                            <RouterLink to="/login" class="link-primary">{{ t('auth.resetPassword.backToLogin') }}</RouterLink>
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
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { apiPost } from '../api';

const route = useRoute();
const { t } = useI18n({ useScope: 'global' });

const logoUrl = '/img/logo.svg';

const token = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');

const submitting = ref(false);
const success = ref('');
const error = ref('');
const errors = ref({});

function fieldError(field) {
    const msg = errors.value?.[field];
    if (Array.isArray(msg)) return msg[0];
    if (typeof msg === 'string') return msg;
    return '';
}

onMounted(() => {
    token.value = String(route.params?.token ?? '');
    email.value = String(route.query?.email ?? '');
});

async function submit() {
    success.value = '';
    error.value = '';
    errors.value = {};
    submitting.value = true;

    try {
        const data = await apiPost('/reset-password', {
            token: token.value,
            email: email.value,
            password: password.value,
            password_confirmation: passwordConfirmation.value,
        });

        success.value = data?.status ?? t('auth.resetPassword.success');
    } catch (e) {
        errors.value = e?.response?.data?.errors ?? {};
        error.value = e?.response?.data?.message ?? t('auth.resetPassword.error');
    } finally {
        submitting.value = false;
    }
}
</script>
