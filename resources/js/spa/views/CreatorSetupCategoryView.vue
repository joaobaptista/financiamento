<template>
    <div class="container py-5" style="max-width: 820px">
        <div class="text-center mb-4">
            <div class="text-uppercase text-muted small">1 de 3</div>
            <h1 class="h3 fw-normal mt-2">Primeiro, vamos te configurar.</h1>
            <p class="text-muted mb-0">
                Selecione uma categoria principal e uma subcategoria para o seu novo perfil de criador.
            </p>
            <p class="text-muted small mb-0">Isso ajuda apoiadores a encontrarem seus projetos. Você pode alterar depois.</p>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row g-3 justify-content-center">
                    <div class="col-12 col-md-5">
                        <label class="form-label">Categoria</label>
                        <select v-model="primaryCategory" class="form-select" :disabled="saving">
                            <option value="">Selecione…</option>
                            <option v-for="c in categories" :key="c.key" :value="c.key">{{ c.label }}</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-5">
                        <label class="form-label">Subcategoria</label>
                        <select v-model="subcategory" class="form-select" :disabled="saving || !primaryCategory">
                            <option value="">Selecione…</option>
                            <option v-for="s in availableSubcategories" :key="s" :value="s">{{ s }}</option>
                        </select>
                    </div>
                </div>

                <hr class="my-4" />

                <div class="d-flex justify-content-end">
                    <button
                        type="button"
                        class="btn btn-dark"
                        :disabled="saving || !primaryCategory || !subcategory"
                        @click="submit"
                    >
                        {{ saving ? 'Salvando…' : 'Próximo' }}
                    </button>
                </div>

                <div v-if="error" class="alert alert-danger mt-3 mb-0" role="alert">{{ error }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { apiGet, apiPost } from '../api';
import { categories } from '../categories';

const props = defineProps({
    user: { type: Object, default: null },
});

const router = useRouter();

const primaryCategory = ref('');
const subcategory = ref('');
const saving = ref(false);
const error = ref('');

const availableSubcategories = computed(() => {
    // MVP: manter simples e consistente. Podemos expandir para uma árvore real por categoria depois.
    if (!primaryCategory.value) return [];
    return ['Geral'];
});

async function loadExisting() {
    try {
        const data = await apiGet('/api/me/creator-profile');
        if (data?.profile) {
            primaryCategory.value = data.profile.primary_category || '';
            subcategory.value = data.profile.subcategory || '';
        }

        if (primaryCategory.value && !subcategory.value) {
            subcategory.value = 'Geral';
        }
    } catch {
        // ignore
    }
}

async function submit() {
    error.value = '';
    saving.value = true;

    try {
        await apiPost('/api/me/creator-profile', {
            primary_category: primaryCategory.value,
            subcategory: subcategory.value,
        });

        // Depois do passo 1, segue para criação de campanha.
        router.push('/me/campaigns/create');
    } catch (e) {
        error.value = e?.response?.data?.message || 'Erro ao salvar.';
    } finally {
        saving.value = false;
    }
}

onMounted(async () => {
    if (!props.user) {
        router.push('/login');
        return;
    }

    await loadExisting();
});
</script>
