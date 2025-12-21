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
                            <h1 class="h3 fw-normal mb-3">Entrar</h1>

                            <button type="button" class="btn btn-outline-secondary w-100 py-2" @click="onGoogleSignIn">
                                <span class="me-2" aria-hidden="true">G</span>
                                Entrar com Google
                            </button>

                            <div class="d-flex align-items-center my-4">
                                <hr class="flex-grow-1" />
                                <span class="mx-3 text-muted small">ou</span>
                                <hr class="flex-grow-1" />
                            </div>

                            <form @submit.prevent="submit">
                                <div class="mb-3">
                                    <input
                                        v-model="email"
                                        type="email"
                                        class="form-control"
                                        placeholder="Email"
                                        autocomplete="email"
                                        required
                                    />
                                </div>

                                <div class="mb-2">
                                    <input
                                        v-model="password"
                                        type="password"
                                        class="form-control"
                                        placeholder="Password"
                                        autocomplete="current-password"
                                        required
                                    />
                                </div>

                                <div class="mb-3">
                                    <a href="#" class="small link-primary" @click.prevent>Forgot your password?</a>
                                </div>

                                <button type="submit" class="btn btn-success w-100 py-2" :disabled="submitting">
                                    {{ submitting ? 'Entrando…' : 'Entrar' }}
                                </button>

                                <div class="form-check mt-3">
                                    <input id="remember" v-model="remember" class="form-check-input" type="checkbox" />
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>

                                <div v-if="error" class="alert alert-danger mt-3 mb-0" role="alert">
                                    {{ error }}
                                </div>

                            </form>
                        </div>

                        <div class="border-top p-3 text-center">
                            <span class="text-muted">Novo por aqui?</span>
                            <RouterLink to="/register" class="link-primary">Cadastre-se</RouterLink>
                        </div>

                        <div class="border-top p-3 text-center text-muted small">
                            This site is protected by reCAPTCHA and the Google
                            <a href="#" class="link-primary" @click.prevent>Privacy Policy</a>
                            and
                            <a href="#" class="link-primary" @click.prevent>Terms of Service</a>
                            apply.
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
import { apiPost } from '../api';

const emit = defineEmits(['auth-updated']);
const router = useRouter();

const logoUrl = '/img/logo.svg';

const email = ref('');
const password = ref('');
const remember = ref(false);
const error = ref('');
const submitting = ref(false);

async function onGoogleSignIn() {
    window.location.href = '/api/oauth/google/redirect';
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
        error.value = e?.response?.data?.message ?? 'Erro ao entrar.';
    } finally {
        submitting.value = false;
    }
}
</script>
