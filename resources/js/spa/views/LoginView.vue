<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="py-3 text-center">
                    <div class="text-uppercase text-muted small">Conta</div>
                    <h1 class="h3 fw-normal mb-0">Entrar</h1>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input v-model="email" type="email" class="form-control" required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Senha</label>
                                <input v-model="password" type="password" class="form-control" required />
                            </div>

                            <button type="submit" class="btn btn-primary w-100" :disabled="submitting">
                                {{ submitting ? 'Entrando…' : 'Entrar' }}
                            </button>

                            <div v-if="error" class="alert alert-danger mt-3 mb-0" role="alert">
                                {{ error }}
                            </div>

                            <div class="text-center mt-3">
                                <RouterLink to="/register" class="btn btn-link">Criar conta</RouterLink>
                            </div>
                        </form>
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

const email = ref('');
const password = ref('');
const error = ref('');
const submitting = ref(false);

async function submit() {
    error.value = '';
    submitting.value = true;

    try {
        await apiPost('/api/login', {
            email: email.value,
            password: password.value,
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
