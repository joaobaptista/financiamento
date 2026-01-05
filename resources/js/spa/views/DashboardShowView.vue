<template>
    <div class="container">
        <div v-if="loading" class="py-5 text-center">
            <div class="spinner-border text-primary mb-2" role="status"></div>
            <div class="text-muted">{{ t('common.loading') }}</div>
        </div>

        <div v-else-if="error" class="py-5 text-center">
            <div class="alert alert-danger">
                {{ error }}
                <div class="mt-2">
                    <button class="btn btn-outline-danger btn-sm" @click="load">
                        <i class="bi bi-arrow-clockwise"></i> Tentar novamente
                    </button>
                </div>
            </div>
        </div>

        <div v-else>
            <div class="mb-4">
                <RouterLink to="/dashboard" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> {{ t('common.back') }}
                </RouterLink>
            </div>

            <div class="py-2">
                <div class="text-uppercase text-muted small">{{ t('dashboard.stats') }}</div>
                <h1 class="h3 fw-normal mb-0">{{ data?.campaign?.title || 'Campanha' }}</h1>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h6 class="text-muted">Total Arrecadado</h6>
                            <h3 class="text-success">{{ formatMoney(data?.campaign?.pledged_amount) }}</h3>
                            <small class="text-muted">{{ t('common.ofGoal', { goal: formatMoney(data?.campaign?.goal_amount) }) }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h6 class="text-muted">{{ t('dashboard.backers') }}</h6>
                            <h3>{{ data?.stats?.total_backers ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h6 class="text-muted">{{ t('dashboard.progress') }}</h6>
                            <h3>{{ Math.round(data?.stats?.progress ?? 0) }}%</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h6 class="text-muted">{{ t('dashboard.daysRemaining') }}</h6>
                            <h3>{{ data?.stats?.days_remaining ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-3">
                <button
                    v-if="data?.campaign?.status === 'draft'"
                    type="button"
                    class="btn btn-success"
                    :disabled="publishing"
                    @click="publish"
                >
                    <i class="bi bi-rocket-takeoff"></i>
                    {{ publishing ? t('common.publishing') : t('common.publish') }}
                </button>

                <button
                    type="button"
                    class="btn btn-outline-danger ms-2"
                    :disabled="publishing"
                    @click="destroyCampaign"
                >
                    <i class="bi bi-trash"></i> {{ t('common.delete') }}
                </button>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="text-uppercase text-muted small">{{ t('dashboard.backersList') }}</div>
                    <div class="d-flex gap-2">
                        <a :href="`/app-export/campaigns/${props.id}/excel`" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </a>
                        <a :href="`/app-export/campaigns/${props.id}/pdf`" target="_blank" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-file-earmark-pdf"></i> Etiquetas
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div v-if="pledgesList.length" class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ t('dashboard.table.backer') }}</th>
                                    <th>{{ t('dashboard.table.amount') }}</th>
                                    <th>{{ t('dashboard.table.date') }}</th>
                                    <th>{{ t('dashboard.table.reward') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="p in pledgesList" :key="p.id">
                                    <td>{{ p.user?.name || t('dashboard.table.anonymous') || 'Apoiador' }}</td>
                                    <td><strong class="text-success">{{ formatMoney(p.amount) }}</strong></td>
                                    <td>{{ formatDateTime(p.paid_at) }}</td>
                                    <td>{{ p.reward?.title || t('campaignShow.freeSupport.label') || 'Apoio livre' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-else class="text-muted text-center py-4">{{ t('dashboard.noPledges') }}</p>
                </div>
            </div>

            <div v-if="message" class="alert alert-info mt-3">{{ message }}</div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, watch, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { apiDelete, apiGet, apiPost } from '../api';

const props = defineProps({
    id: { type: [String, Number], required: true },
});

const loading = ref(true);
const error = ref('');
const data = ref(null);
const publishing = ref(false);
const exporting = ref(false);
const message = ref('');

const { t, locale } = useI18n({ useScope: 'global' });

const pledgesList = computed(() => {
    const p = data.value?.pledges;
    if (!p) return [];
    return p.data || (Array.isArray(p) ? p : []);
});

function formatMoney(cents) {
    const value = Number(cents || 0) / 100;
    const intlLocale = String(locale.value || 'pt_BR').replace('_', '-');
    return new Intl.NumberFormat(intlLocale, { style: 'currency', currency: 'BRL' }).format(value);
}

function formatDateTime(iso) {
    if (!iso) return '-';
    try {
        const date = new Date(iso);
        const intlLocale = String(locale.value || 'pt_BR').replace('_', '-');
        return date.toLocaleString(intlLocale);
    } catch (e) {
        return iso;
    }
}

async function load() {
    loading.value = true;
    error.value = '';
    try {
        const payload = await apiGet(`/api/dashboard/campaigns/${props.id}`);
        if (!payload) throw new Error('Resposta vazia da API');
        
        data.value = {
            ...payload,
            campaign: payload?.campaign?.data ?? payload?.campaign,
            pledges: payload?.pledges?.data ?? payload?.pledges,
        };
    } catch (e) {
        console.error('Failed to load dashboard data:', e);
        error.value = e?.response?.data?.message ?? 'Não foi possível carregar os dados da campanha.';
    } finally {
        loading.value = false;
    }
}

async function publish() {
    message.value = '';
    publishing.value = true;

    try {
        await apiPost(`/api/me/campaigns/${props.id}/publish`, {});
        message.value = t('dashboard.publishDone');
        await load();
    } catch (e) {
        message.value = e?.response?.data?.message ?? t('dashboard.publishError');
    } finally {
        publishing.value = false;
    }
}

async function destroyCampaign() {
    message.value = '';
    if (!confirm(t('dashboard.deleteConfirm'))) return;

    try {
        await apiDelete(`/api/me/campaigns/${props.id}`);
        message.value = t('dashboard.deleteSuccess');
        // Redirecionar após excluir
        window.location.href = '/dashboard';
    } catch (e) {
        message.value = e?.response?.data?.message ?? t('dashboard.deleteError');
    }
}

onMounted(load);
watch(() => props.id, load);
</script>
