<template>
    <div class="container">
        <div class="py-3">
            <div class="text-uppercase text-muted small">Descobrir</div>
            <h1 class="h3 fw-normal mb-0">Explorar campanhas</h1>

            <div v-if="activeFilterLabel" class="text-muted small mt-1">
                Filtro: <strong>{{ activeFilterLabel }}</strong>
                <RouterLink class="ms-2" :to="{ path: '/campaigns' }">limpar</RouterLink>
            </div>
        </div>

        <div v-if="loading" class="text-center text-muted py-5">Carregando campanhas…</div>

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
                                    aria-label="Ver campanha"
                                ></RouterLink>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="filteredCampaigns.length === 0" class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="text-muted mt-3">Nenhuma campanha encontrada.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { apiGet } from '../api';
import { absoluteUrl } from '../seo';

const route = useRoute();

const loading = ref(true);
const campaigns = ref([]);

const queryQ = computed(() => String(route.query.q || '').trim());
const queryCategory = computed(() => String(route.query.category || '').trim());

const activeFilterLabel = computed(() => {
    if (queryQ.value) return `Busca: ${queryQ.value}`;
    if (queryCategory.value) return `Categoria: ${queryCategory.value}`;
    return '';
});

const filteredCampaigns = computed(() => {
    const all = campaigns.value || [];
    const q = queryQ.value.toLowerCase();
    const category = queryCategory.value.toLowerCase();

    if (!q && !category) return all;

    return all.filter((c) => {
        const haystack = `${c?.title || ''} ${c?.description || ''}`.toLowerCase();
        if (q && !haystack.includes(q)) return false;
        if (category && !haystack.includes(category)) return false;
        return true;
    });
});

function formatMoney(cents) {
    const value = (Number(cents || 0) / 100).toFixed(2);
    return `R$ ${value}`;
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

onMounted(async () => {
    const data = await apiGet('/api/campaigns');
    campaigns.value = data.data ?? [];
    loading.value = false;
});
</script>
