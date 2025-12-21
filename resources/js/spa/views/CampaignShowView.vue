<template>
    <div class="container">
        <div v-if="loading" class="text-muted">Carregando…</div>

        <div v-else-if="campaign" class="row">
            <div class="col-lg-8">
                <div class="py-3">
                    <div class="text-uppercase text-muted small">Projeto</div>
                    <h1 class="h3 fw-normal mb-1">{{ campaign.title }}</h1>
                    <p class="text-muted mb-0">Por {{ campaign.user?.name ?? '—' }}</p>
                </div>

                <div class="card campaign-card mb-4">
                    <div class="ratio ratio-16x9 bg-light">
                        <img
                            v-if="campaign.cover_image_path"
                            :src="campaign.cover_image_path"
                            class="w-100 h-100"
                            :alt="campaign.title"
                            style="object-fit: cover"
                        />
                        <div v-else class="d-flex align-items-center justify-content-center text-muted">
                            <i class="bi bi-image" style="font-size: 2rem"></i>
                        </div>
                    </div>
                </div>

                <div class="my-4">
                    <div class="progress mb-2" style="height: 20px;">
                        <div
                            class="progress-bar bg-success"
                            :style="{ width: `${progress}%` }"
                        >
                            {{ Math.round(progress) }}%
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-success">{{ formatMoney(campaign.pledged_amount) }}</h4>
                            <small class="text-muted">arrecadado de {{ formatMoney(campaign.goal_amount) }}</small>
                        </div>
                        <div class="col-4">
                            <h4>{{ campaign.supporters_count ?? 0 }}</h4>
                            <small class="text-muted">apoiadores</small>
                        </div>
                        <div class="col-4">
                            <h4>{{ daysRemaining }}</h4>
                            <small class="text-muted">dias restantes</small>
                        </div>
                    </div>
                </div>

                <hr />

                <h2 class="h5 fw-normal">Sobre o projeto</h2>
                <div class="mb-4" style="white-space: pre-line;">
                    {{ campaign.description }}
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h2 class="h6 text-uppercase text-muted mb-3">Apoiar</h2>

                        <template v-if="user">
                            <form @submit.prevent="submit">
                                <div class="mb-3">
                                    <label class="form-label">Valor do Apoio (R$)</label>
                                    <input
                                        v-model="amount"
                                        type="number"
                                        class="form-control"
                                        min="1"
                                        step="0.01"
                                        required
                                    />
                                </div>

                                <div v-if="(campaign.rewards || []).length" class="mb-3">
                                    <label class="form-label">Recompensa (Opcional)</label>
                                    <select v-model="rewardId" class="form-select">
                                        <option :value="null">Sem recompensa</option>
                                        <option
                                            v-for="r in campaign.rewards"
                                            :key="r.id"
                                            :value="r.id"
                                            :disabled="!isRewardAvailable(r)"
                                        >
                                            {{ r.title }} - {{ formatMoney(r.min_amount) }}
                                            <template v-if="!isRewardAvailable(r)"> (Esgotado)</template>
                                        </option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-success w-100" :disabled="submitting">
                                    <i class="bi bi-heart-fill"></i>
                                    {{ submitting ? 'Processando…' : 'Apoiar Agora' }}
                                </button>

                                <div v-if="message" class="text-muted small mt-2">{{ message }}</div>
                            </form>
                        </template>

                        <template v-else>
                            <p class="text-muted">Faça login para apoiar este projeto.</p>
                            <RouterLink to="/login" class="btn btn-primary w-100">Entrar</RouterLink>
                            <RouterLink to="/register" class="btn btn-outline-primary w-100 mt-2">Cadastrar</RouterLink>
                        </template>
                    </div>
                </div>

                <div v-if="(campaign.rewards || []).length" class="card mt-3">
                    <div class="card-body">
                        <h2 class="h6 text-uppercase text-muted mb-3">Recompensas</h2>
                        <div v-for="r in campaign.rewards" :key="r.id" class="border-bottom pb-3 mb-3">
                            <h6>{{ r.title }}</h6>
                            <p class="text-muted small mb-1">{{ r.description }}</p>
                            <strong class="text-success">{{ formatMoney(r.min_amount) }}</strong>
                            <small v-if="r.quantity" class="text-muted d-block">
                                {{ r.remaining }}/{{ r.quantity }} disponíveis
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="text-muted">Campanha não encontrada.</div>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { apiGet, apiPost } from '../api';

const props = defineProps({
    slug: { type: String, required: true },
    user: { type: Object, default: null },
});

const loading = ref(true);
const campaign = ref(null);
const amount = ref('10.00');
const rewardId = ref(null);
const submitting = ref(false);
const message = ref('');

function formatMoney(cents) {
    const value = (Number(cents || 0) / 100).toFixed(2);
    return `R$ ${value}`;
}

function isRewardAvailable(r) {
    if (r.quantity == null) return true;
    return Number(r.remaining || 0) > 0;
}

function calcProgress(c) {
    const goal = Number(c?.goal_amount || 0);
    const pledged = Number(c?.pledged_amount || 0);
    if (!goal) return 0;
    return Math.min((pledged / goal) * 100, 100);
}

function calcDaysRemaining(c) {
    if (!c?.ends_at) return 0;
    const end = new Date(c.ends_at);
    const now = new Date();
    const diff = end.getTime() - now.getTime();
    return Math.max(Math.ceil(diff / (1000 * 60 * 60 * 24)), 0);
}

const progress = ref(0);
const daysRemaining = ref(0);

async function fetchCampaign() {
    loading.value = true;
    campaign.value = await apiGet(`/api/campaigns/${props.slug}`);
    progress.value = calcProgress(campaign.value);
    daysRemaining.value = calcDaysRemaining(campaign.value);
    loading.value = false;
}

async function submit() {
    message.value = '';
    submitting.value = true;

    try {
        await apiPost('/api/pledges', {
            campaign_id: campaign.value.id,
            amount: amount.value,
            reward_id: rewardId.value,
        });
        message.value = 'Apoio realizado com sucesso!';
        await fetchCampaign();
    } catch (e) {
        message.value = e?.response?.data?.message ?? 'Erro ao apoiar.';
    } finally {
        submitting.value = false;
    }
}

onMounted(fetchCampaign);
watch(() => props.slug, fetchCampaign);
</script>
