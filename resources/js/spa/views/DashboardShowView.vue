<template>
    <div class="container">
        <div v-if="loading" class="text-muted">{{ t('common.loading') }}</div>

        <div v-else>
            <div class="mb-4">
                <RouterLink to="/dashboard" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> {{ t('common.back') }}
                </RouterLink>
            </div>

            <div class="py-2">
                <div class="text-uppercase text-muted small">{{ t('dashboard.stats') }}</div>
                <h1 class="h3 fw-normal mb-0">{{ data?.campaign?.title }}</h1>
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
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="text-uppercase text-muted small">{{ t('dashboard.backersList') }}</div>
                </div>
                <div class="card-body">
                    <div v-if="(data?.pledges || []).length" class="table-responsive">
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
                                <tr v-for="p in data.pledges" :key="p.id">
                                    <td>{{ p.user?.name ?? '-' }}</td>
                                    <td><strong class="text-success">{{ formatMoney(p.amount) }}</strong></td>
                                    <td>{{ formatDateTime(p.paid_at) }}</td>
                                    <td>{{ p.reward?.title ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-else class="text-muted text-center py-4">{{ t('dashboard.noPledges') }}</p>
                </div>
            </div>

            <div v-if="message" class="text-muted mt-3">{{ message }}</div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { apiGet, apiPost } from '../api';

const props = defineProps({
    id: { type: [String, Number], required: true },
});

const loading = ref(true);
const data = ref(null);
const publishing = ref(false);
const message = ref('');

const { t, locale } = useI18n({ useScope: 'global' });

function formatMoney(cents) {
    const value = Number(cents || 0) / 100;
    const intlLocale = String(locale.value || 'pt_BR').replace('_', '-');
    return new Intl.NumberFormat(intlLocale, { style: 'currency', currency: 'BRL' }).format(value);
}

function formatDateTime(iso) {
    if (!iso) return '-';
    const date = new Date(iso);
        const intlLocale = String(locale.value || 'pt_BR').replace('_', '-');
        return date.toLocaleString(intlLocale);
}

async function load() {
    loading.value = true;
    data.value = await apiGet(`/api/dashboard/campaigns/${props.id}`);
    loading.value = false;
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

onMounted(load);
watch(() => props.id, load);
</script>
