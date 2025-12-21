<template>
    <div class="container py-5" style="max-width: 820px">
        <div class="text-center mb-4">
            <div class="text-uppercase text-muted small">{{ t('creatorSetup.stepLabel') }}</div>
            <h1 class="h3 fw-normal mt-2">{{ t('creatorSetup.title') }}</h1>
            <p class="text-muted mb-0">
                {{ t('creatorSetup.subtitle') }}
            </p>
            <p class="text-muted small mb-0">{{ t('creatorSetup.helper') }}</p>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row g-3 justify-content-center">
                    <div class="col-12 col-md-5">
                        <label class="form-label">{{ t('creatorSetup.categoryLabel') }}</label>
                        <select v-model="primaryCategory" class="form-select" :disabled="saving">
                            <option value="">{{ t('creatorSetup.selectPlaceholder') }}</option>
                            <option v-for="c in categories" :key="c.key" :value="c.key">{{ t(c.labelKey) }}</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-5">
                        <label class="form-label">{{ t('creatorSetup.subcategoryLabel') }}</label>
                        <select v-model="subcategory" class="form-select" :disabled="saving || !primaryCategory">
                            <option value="">{{ t('creatorSetup.selectPlaceholder') }}</option>
                            <option v-for="s in availableSubcategories" :key="s.value" :value="s.value">{{ s.label }}</option>
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
                        {{ saving ? t('common.saving') : t('common.next') }}
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
import { useI18n } from 'vue-i18n';
import { apiGet, apiPost } from '../api';
import { categories } from '../categories';

const props = defineProps({
    user: { type: Object, default: null },
});

const router = useRouter();
const { t } = useI18n({ useScope: 'global' });

const primaryCategory = ref('');
const subcategory = ref('');
const saving = ref(false);
const error = ref('');

const availableSubcategories = computed(() => {
    // MVP: manter simples e consistente. Podemos expandir para uma árvore real por categoria depois.
    if (!primaryCategory.value) return [];
    return [{ value: 'Geral', label: t('creatorSetup.subcategories.general') }];
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
        error.value = e?.response?.data?.message || t('errors.saveFailed');
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
