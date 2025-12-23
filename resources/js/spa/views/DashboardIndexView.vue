<template>
    <div class="container">
        <div class="py-3">
            <div class="d-flex justify-content-between align-items-end">
                <div>
                    <div class="text-uppercase text-muted small">{{ t('dashboard.sectionLabel') }}</div>
                    <h1 class="h3 fw-normal mb-0">{{ t('dashboard.title') }}</h1>
                </div>
                <RouterLink to="/me/campaigns/create" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> {{ t('dashboard.newCampaign') }}
                </RouterLink>
            </div>
        </div>

        <div v-if="!user" class="text-muted">{{ t('errors.loginRequired') }}</div>

        <div v-else-if="loading" class="text-muted">{{ t('common.loading') }}</div>

        <template v-else>
            <div v-for="c in campaigns" :key="c.id" class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5>{{ c.title }}</h5>
                            <p class="text-muted mb-2">{{ limit(c.description, 150) }}</p>

                            <div class="progress mb-2" style="height: 10px;">
                                <div class="progress-bar bg-success" :style="{ width: `${progress(c)}%` }"></div>
                            </div>

                            <div class="d-flex gap-4 text-sm">
                                <span>
                                    <strong>{{ formatMoney(c.pledged_amount) }}</strong> {{ t('common.ofGoal', { goal: formatMoney(c.goal_amount) }) }}
                                </span>
                                <span>{{ t('dashboard.pledgesCount', { count: c.pledges_count ?? 0 }) }}</span>
                                <span>{{ t('common.daysRemaining', { days: daysRemaining(c) }) }}</span>
                            </div>
                        </div>

                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <span :class="`badge ${statusBadge(c.status)} mb-2`">
                                {{ statusLabel(c.status) }}
                            </span>

                            <div class="btn-group d-block">
                                <template v-if="c.status === 'draft'">
                                    <RouterLink :to="`/me/campaigns/${c.id}/edit`" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> {{ t('common.edit') }}
                                    </RouterLink>
                                    <button type="button" class="btn btn-sm btn-success" @click="publish(c.id)">
                                        <i class="bi bi-rocket-takeoff"></i> {{ t('common.publish') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="destroyCampaign(c)">
                                        <i class="bi bi-trash"></i> {{ t('common.delete') }}
                                    </button>
                                </template>
                                <template v-else>
                                    <RouterLink :to="`/campaigns/${c.slug}`" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> {{ t('common.view') }}
                                    </RouterLink>
                                    <RouterLink :to="`/dashboard/campaigns/${c.id}`" class="btn btn-sm btn-primary">
                                        <i class="bi bi-bar-chart"></i> {{ t('dashboard.stats') }}
                                    </RouterLink>
                                    <RouterLink :to="`/me/campaigns/${c.id}/edit`" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> {{ t('common.edit') }}
                                    </RouterLink>
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="destroyCampaign(c)">
                                        <i class="bi bi-trash"></i> {{ t('common.delete') }}
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="campaigns.length === 0" class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="text-muted mt-3">{{ t('dashboard.empty') }}</p>
                <RouterLink to="/me/campaigns/create" class="btn btn-primary">
                    {{ t('dashboard.createFirst') }}
                </RouterLink>
            </div>
        </template>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { apiDelete, apiGet, apiPost } from '../api';

const props = defineProps({
    user: { type: Object, default: null },
});

const loading = ref(true);
const campaigns = ref([]);

const { t, locale } = useI18n({ useScope: 'global' });

const emit = defineEmits(['flash-success', 'flash-error']);

async function load() {
    if (!props.user) {
        campaigns.value = [];
        loading.value = false;
        return;
    }

    loading.value = true;
    const data = await apiGet('/api/dashboard/campaigns');
    campaigns.value = data.data ?? data;
    loading.value = false;
}

function limit(text, max) {
    const value = String(text || '');
    if (value.length <= max) return value;
    return `${value.slice(0, max)}…`;
}

function formatMoney(cents) {
    const value = Number(cents || 0) / 100;
    const intlLocale = String(locale.value || 'pt_BR').replace('_', '-');
    return new Intl.NumberFormat(intlLocale, { style: 'currency', currency: 'BRL' }).format(value);
}

function progress(c) {
    const goal = Number(c.goal_amount || 0);
    const pledged = Number(c.pledged_amount || 0);
    if (!goal) return 0;
    return Math.min((pledged / goal) * 100, 100);
}

function daysRemaining(c) {
    if (!c.ends_at) return 0;
    const end = new Date(c.ends_at);
    const now = new Date();
    const diff = end.getTime() - now.getTime();
    return Math.max(Math.ceil(diff / (1000 * 60 * 60 * 24)), 0);
}

function statusBadge(status) {
    if (status === 'active') return 'bg-success';
    if (status === 'draft') return 'bg-secondary';
    return 'bg-primary';
}

function statusLabel(status) {
    if (status === 'draft') return t('campaign.status.draft');
    if (status === 'active') return t('campaign.status.active');
    if (status === 'closed') return t('campaign.status.closed');
    return String(status || '');
}

async function destroyCampaign(campaign) {
    if (!campaign?.id) return;
    if (!confirm(t('dashboard.deleteConfirm'))) return;

    try {
        await apiDelete(`/api/me/campaigns/${campaign.id}`);
        emit('flash-success', t('dashboard.deleteSuccess'));
        await load();
    } catch (e) {
        emit('flash-error', e?.response?.data?.message ?? t('dashboard.deleteError'));
    }
}

async function publish(id) {
    try {
        await apiPost(`/api/me/campaigns/${id}/publish`, {});
        emit('flash-success', t('dashboard.publishSuccess'));
        await load();
    } catch (e) {
        emit('flash-error', e?.response?.data?.message ?? t('dashboard.publishError'));
    }
}

onMounted(load);
watch(() => props.user, load);
</script>
