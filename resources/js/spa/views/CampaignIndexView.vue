<template>
    <div class="container">
        <div class="py-3">
            <div class="text-uppercase text-muted small">{{ t('campaignsIndex.discoverLabel') }}</div>
            <h1 class="h3 fw-normal mb-0">{{ t('campaignsIndex.title') }}</h1>

            <div v-if="activeFilterLabel" class="text-muted small mt-1">
                {{ t('campaignsIndex.filterPrefix') }} <strong>{{ activeFilterLabel }}</strong>
                <RouterLink class="ms-2" :to="{ path: '/campaigns' }">{{ t('campaignsIndex.clearFilter') }}</RouterLink>
            </div>
        </div>

        <div v-if="loading" class="text-center text-muted py-5">{{ t('campaignsIndex.loading') }}</div>

        <div v-else-if="loadError" class="alert alert-danger" role="alert">
            {{ loadError }}
        </div>

        <div v-else>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                <div v-for="c in filteredCampaigns" :key="c.id" class="col">
                    <div class="card campaign-card h-100">
                        <div class="ratio ratio-16x9 bg-light">
                            <template v-if="c.cover_image_path">
                                <picture v-if="c.cover_image_webp_path">
                                    <source :srcset="absoluteUrl(c.cover_image_webp_path)" type="image/webp" />
                                    <img
                                        :src="absoluteUrl(c.cover_image_path)"
                                        class="w-100 h-100"
                                        :alt="c.title"
                                        style="object-fit: cover"
                                    />
                                </picture>
                                <img
                                    v-else
                                    :src="absoluteUrl(c.cover_image_path)"
                                    class="w-100 h-100"
                                    :alt="c.title"
                                    style="object-fit: cover"
                                />
                            </template>
                            <div v-else class="d-flex align-items-center justify-content-center text-muted">
                                <i class="bi bi-image" style="font-size: 1.5rem"></i>
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h2 class="h6 mb-1">{{ limit(c.title, 70) }}</h2>
                            <p class="text-muted small mb-2">{{ limit(c.description, 90) }}</p>

                            <div class="mt-auto">
                                <div class="d-flex justify-content-between small text-muted mb-2">
                                    <span>{{ daysRemaining(c) }} dias</span>
                                    <span class="text-success fw-semibold">{{ Math.round(progress(c)) }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" :style="{ width: `${progress(c)}%` }"></div>
                                </div>
                                <RouterLink
                                    :to="`/campaigns/${c.slug}`"
                                    class="stretched-link"
                                    :aria-label="t('common.viewCampaign')"
                                ></RouterLink>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="filteredCampaigns.length === 0" class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="text-muted mt-3">{{ t('campaignsIndex.empty') }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { apiGet } from '../api';
import { absoluteUrl } from '../seo';
import { categories } from '../categories';

const route = useRoute();

const loading = ref(true);
const campaigns = ref([]);
const loadError = ref('');

const { t, locale } = useI18n({ useScope: 'global' });

const queryQ = computed(() => String(route.query.q || '').trim());
const queryCategory = computed(() => String(route.query.category || '').trim());

const categoryLabel = computed(() => {
    const key = queryCategory.value;
    if (!key) return '';
    const found = categories.find(c => c.key === key);
    if (!found) return key;
    return t(found.labelKey);
});

const activeFilterLabel = computed(() => {
    if (queryQ.value) return t('campaignsIndex.filters.search', { q: queryQ.value });
    if (queryCategory.value) return t('campaignsIndex.filters.category', { category: categoryLabel.value });
    return '';
});

const filteredCampaigns = computed(() => campaigns.value || []);

function formatMoney(cents) {
    const value = Number(cents || 0) / 100;
    const intlLocale = String(locale.value || 'pt_BR').replace('_', '-');
    return new Intl.NumberFormat(intlLocale, { style: 'currency', currency: 'BRL' }).format(value);
}

function limit(text, max) {
    const value = String(text || '');
    if (value.length <= max) return value;
    return `${value.slice(0, max)}…`;
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

async function load() {
    loadError.value = '';
    loading.value = true;

    try {
        const params = new URLSearchParams();
        if (queryQ.value) params.set('q', queryQ.value);
        if (queryCategory.value) params.set('category', queryCategory.value);

        const url = params.toString() ? `/api/campaigns?${params.toString()}` : '/api/campaigns';
        const data = await apiGet(url);
        campaigns.value = data.data ?? [];
    } catch (error) {
        console.error('Failed to load /api/campaigns', error);
        campaigns.value = [];
        loadError.value = t('errors.loadFailed');
    } finally {
        loading.value = false;
    }
}

onMounted(load);
watch(() => route.query, load);
</script>
