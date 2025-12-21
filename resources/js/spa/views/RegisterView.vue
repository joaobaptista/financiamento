<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="py-3 text-center">
                    <div class="text-uppercase text-muted small">Conta</div>
                    <h1 class="h3 fw-normal mb-0">Criar conta</h1>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <label class="form-label">Nome</label>
                                <input v-model="name" type="text" class="form-control" required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input v-model="email" type="email" class="form-control" required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Senha</label>
                                <input v-model="password" type="password" class="form-control" required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirmar senha</label>
                                <input v-model="passwordConfirmation" type="password" class="form-control" required />
                            </div>

                            <button type="submit" class="btn btn-primary w-100" :disabled="submitting">
                                {{ submitting ? 'Criando…' : 'Criar conta' }}
                            </button>

                            <div v-if="error" class="alert alert-danger mt-3 mb-0" role="alert">
                                {{ error }}
                            </div>

                            <div class="text-center mt-3">
                                <RouterLink to="/login" class="btn btn-link">Já tenho conta</RouterLink>
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

const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const error = ref('');
const submitting = ref(false);

async function submit() {
    error.value = '';
    submitting.value = true;

    try {
        await apiPost('/api/register', {
            name: name.value,
            email: email.value,
            password: password.value,
            password_confirmation: passwordConfirmation.value,
        });
        emit('auth-updated');
        router.push('/dashboard');
    } catch (e) {
        error.value = e?.response?.data?.message ?? 'Erro ao criar conta.';
    } finally {
        submitting.value = false;
    }
}
</script>
