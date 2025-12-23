<template>
    <div class="container">
        <div class="text-center py-3 py-md-4">
            <h1 class="display-6 fw-normal mb-0">{{ t('home.heroTitle') }}</h1>
        </div>

        <div v-if="loading" class="text-center text-muted py-5">{{ t('common.loading') }}</div>

        <div v-else-if="loadError" class="alert alert-danger" role="alert">
            {{ loadError }}
        </div>

        <div v-else class="row g-4 align-items-start">
            <div class="col-lg-6">
                <div class="text-uppercase text-muted small mb-2">{{ t('home.featuredLabel') }}</div>

                <div v-if="featuredCampaign" class="card campaign-card">
                    <div class="ratio ratio-16x9 bg-light">
                        <template v-if="featuredCampaign.cover_image_path">
                            <picture v-if="featuredCampaign.cover_image_webp_path">
                                <source :srcset="absoluteUrl(featuredCampaign.cover_image_webp_path)" type="image/webp" />
                                <img
                                    :src="absoluteUrl(featuredCampaign.cover_image_path)"
                                    class="w-100 h-100"
                                    :alt="featuredCampaign.title"
                                    style="object-fit: cover"
                                />
                            </picture>
                            <img
                                v-else
                                :src="absoluteUrl(featuredCampaign.cover_image_path)"
                                class="w-100 h-100"
                                :alt="featuredCampaign.title"
                                style="object-fit: cover"
                            />
                        </template>
                        <div v-else class="d-flex align-items-center justify-content-center text-muted">
                            <i class="bi bi-image" style="font-size: 2rem"></i>
                        </div>
                    </div>

                    <div class="card-body">
                        <h3 class="h5 mb-1">{{ featuredCampaign.title }}</h3>
                        <p class="text-muted mb-3">{{ limit(featuredCampaign.description, 140) }}</p>

                        <div class="progress mb-2">
                            <div
                                class="progress-bar bg-success"
                                :style="{ width: `${progress(featuredCampaign)}%` }"
                            ></div>
                        </div>

                        <div class="d-flex justify-content-between small">
                            <span class="text-success fw-semibold">{{ t('common.percentFunded', { percent: Math.round(progress(featuredCampaign)) }) }}</span>
                            <span class="text-muted">{{ t('common.daysRemaining', { days: daysRemaining(featuredCampaign) }) }}</span>
                        </div>

                        <div class="mt-2">
                            <strong>{{ formatMoney(featuredCampaign.pledged_amount) }}</strong>
                            <span class="text-muted">{{ t('common.ofGoal', { goal: formatMoney(featuredCampaign.goal_amount) }) }}</span>
                        </div>

                        <RouterLink :to="`/campaigns/${featuredCampaign.slug}`" class="btn btn-outline-primary mt-3">
                            {{ t('common.viewProject') }}
                        </RouterLink>
                    </div>
                </div>

                <div v-else class="text-muted">{{ t('home.noActiveCampaign') }}</div>
            </div>

            <div class="col-lg-6">
                <div class="text-uppercase text-muted small mb-2">{{ t('home.recommendedLabel') }}</div>

                <div class="row row-cols-1 row-cols-md-2 g-3">
                    <div v-for="c in recommended" :key="c.id" class="col">
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
                                <h3 class="h6 mb-1">{{ limit(c.title, 60) }}</h3>
                                <p class="text-muted small mb-2">{{ limit(c.description, 80) }}</p>

                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between small text-muted mb-2">
                                        <span>{{ t('common.daysShort', { days: daysRemaining(c) }) }}</span>
                                        <span class="text-success fw-semibold">{{ Math.round(progress(c)) }}%</span>
                                    </div>

                                    <div class="progress">
                                        <div class="progress-bar bg-success" :style="{ width: `${progress(c)}%` }"></div>
                                    </div>

                                    <RouterLink
                                        :to="`/campaigns/${c.slug}`"
                                        class="stretched-link"
                                        :aria-label="t('common.viewProject')"
                                    ></RouterLink>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <RouterLink to="/campaigns" class="btn btn-outline-primary">{{ t('home.discoverMore') }}</RouterLink>
                </div>
            </div>
        </div>

        <div class="py-5"></div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { apiGet } from '../api';
import { absoluteUrl } from '../seo';

defineProps({
    user: { type: Object, default: null },
});

const loading = ref(true);
const campaigns = ref([]);
const loadError = ref('');

const { t, locale } = useI18n({ useScope: 'global' });

const featuredCampaign = computed(() => campaigns.value[0] || null);
const recommended = computed(() => campaigns.value.slice(1, 5));

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

function formatMoney(cents) {
    const value = Number(cents || 0) / 100;
    const intlLocale = String(locale.value || 'pt_BR').replace('_', '-');
    return new Intl.NumberFormat(intlLocale, { style: 'currency', currency: 'BRL' }).format(value);
}

onMounted(async () => {
    loadError.value = '';
    loading.value = true;

    try {
        const data = await apiGet('/api/campaigns');
        const all = data.data ?? [];
        campaigns.value = all.slice(0, 5);
    } catch (error) {
        console.error('Failed to load /api/campaigns', error);
        campaigns.value = [];
        loadError.value = t('errors.loadFailed');
    } finally {
        loading.value = false;
    }
});
</script>
