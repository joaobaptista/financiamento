<template>
    <div class="container">
        <div class="py-3">
            <div class="d-flex justify-content-between align-items-end">
                <div>
                    <div class="text-uppercase text-muted small">Criador</div>
                    <h1 class="h3 fw-normal mb-0">Minhas campanhas</h1>
                </div>
                <RouterLink to="/me/campaigns/create" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nova campanha
                </RouterLink>
            </div>
        </div>

        <div v-if="!user" class="text-muted">Você precisa estar logado.</div>

        <div v-else-if="loading" class="text-muted">Carregando…</div>

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
                                    <strong>{{ formatMoney(c.pledged_amount) }}</strong> de {{ formatMoney(c.goal_amount) }}
                                </span>
                                <span>{{ c.pledges_count ?? 0 }} apoios</span>
                                <span>{{ daysRemaining(c) }} dias restantes</span>
                            </div>
                        </div>

                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <span :class="`badge ${statusBadge(c.status)} mb-2`">
                                {{ capitalize(c.status) }}
                            </span>

                            <div class="btn-group d-block">
                                <template v-if="c.status === 'draft'">
                                    <RouterLink :to="`/me/campaigns/${c.id}/edit`" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Editar
                                    </RouterLink>
                                    <button type="button" class="btn btn-sm btn-success" @click="publish(c.id)">
                                        <i class="bi bi-rocket-takeoff"></i> Publicar
                                    </button>
                                </template>
                                <template v-else>
                                    <RouterLink :to="`/campaigns/${c.slug}`" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Ver
                                    </RouterLink>
                                    <RouterLink :to="`/dashboard/campaigns/${c.id}`" class="btn btn-sm btn-primary">
                                        <i class="bi bi-bar-chart"></i> Estatísticas
                                    </RouterLink>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="campaigns.length === 0" class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="text-muted mt-3">Você ainda não criou nenhuma campanha.</p>
                <RouterLink to="/me/campaigns/create" class="btn btn-primary">
                    Criar Minha Primeira Campanha
                </RouterLink>
            </div>
        </template>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { apiGet, apiPost } from '../api';

const props = defineProps({
    user: { type: Object, default: null },
});

const loading = ref(true);
const campaigns = ref([]);

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
    const value = (Number(cents || 0) / 100).toFixed(2);
    return `R$ ${value}`;
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

function capitalize(text) {
    const value = String(text || '');
    return value ? value.charAt(0).toUpperCase() + value.slice(1) : value;
}

async function publish(id) {
    try {
        await apiPost(`/api/me/campaigns/${id}/publish`, {});
        emit('flash-success', 'Campanha publicada com sucesso!');
        await load();
    } catch (e) {
        emit('flash-error', e?.response?.data?.message ?? 'Erro ao publicar.');
    }
}

onMounted(load);
watch(() => props.user, load);
</script>
