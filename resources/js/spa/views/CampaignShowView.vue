<template>
    <div>
        <div v-if="loading" class="container py-4 text-muted">Carregando…</div>

        <div v-else-if="campaign">
            <!-- Hero (padrão Catarse) -->
            <div class="bg-light border-bottom">
                <div class="container py-4 py-md-5">
                    <div class="text-uppercase text-muted small">Campanha</div>
                    <h1 class="display-6 mb-2">{{ campaign.title }}</h1>
                    <div class="text-muted">
                        Por <strong>{{ campaign.user?.name ?? '—' }}</strong>
                        <span class="mx-2">•</span>
                        <span v-if="isCampaignOpen">{{ daysRemaining }} dias restantes</span>
                        <span v-else>Campanha finalizada</span>
                    </div>
                </div>
            </div>

            <div class="container py-4">
                <div class="row g-4">
                    <!-- Conteúdo / História -->
                    <div class="col-lg-8">
                        <div class="card mb-3">
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

                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h5 fw-normal mb-3">Sobre o projeto</h2>
                                <div
                                    v-if="storyHtml"
                                    class="campaign-story text-muted"
                                    v-html="storyHtml"
                                ></div>
                                <div v-else class="text-muted" style="white-space: pre-line">
                                    {{ campaign.description }}
                                </div>
                            </div>
                        </div>

                        <div v-if="(campaign.rewards || []).length" class="card" id="recompensas">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <h2 class="h5 fw-normal mb-0">Recompensas</h2>
                                    <span class="text-muted small">Escolha uma recompensa para aumentar sua contribuição.</span>
                                </div>

                                <div class="mt-3">
                                    <div class="card border mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start gap-3">
                                                <div>
                                                    <div class="text-uppercase text-muted small">Apoio livre</div>
                                                    <div class="fw-semibold">Contribua com qualquer valor</div>
                                                    <div class="text-muted small">Ajude o projeto sem selecionar recompensa.</div>
                                                </div>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-primary"
                                                    @click="selectNoReward()"
                                                >
                                                    Selecionar
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-for="r in sortedRewards" :key="r.id" class="card border mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start gap-3">
                                                <div>
                                                    <div class="text-success fw-semibold">A partir de {{ formatMoney(r.min_amount) }}</div>
                                                    <div class="fw-semibold">{{ r.title }}</div>
                                                    <div v-if="r.description" class="text-muted small mt-1">{{ r.description }}</div>
                                                    <div v-if="r.quantity" class="text-muted small mt-2">
                                                        <span v-if="isRewardAvailable(r)">{{ r.remaining }}/{{ r.quantity }} disponíveis</span>
                                                        <span v-else class="text-danger">Esgotada</span>
                                                    </div>
                                                </div>

                                                <button
                                                    type="button"
                                                    class="btn"
                                                    :class="isRewardAvailable(r) ? 'btn-primary' : 'btn-outline-secondary'"
                                                    :disabled="!isRewardAvailable(r)"
                                                    @click="selectReward(r)"
                                                >
                                                    {{ isRewardAvailable(r) ? 'Selecionar' : 'Indisponível' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar de apoio -->
                    <div class="col-lg-4">
                        <div ref="supportBox" class="card sticky-top" style="top: 16px">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="text-uppercase text-muted small">Apoiar</div>
                                    <a
                                        v-if="(campaign.rewards || []).length"
                                        class="small text-decoration-none"
                                        href="#recompensas"
                                    >
                                        ver recompensas
                                    </a>
                                </div>

                                <div class="mt-3">
                                    <div class="progress" style="height: 10px">
                                        <div class="progress-bar bg-success" :style="{ width: `${progress}%` }"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <div>
                                            <div class="h4 text-success mb-0">{{ formatMoney(campaign.pledged_amount) }}</div>
                                            <div class="text-muted small">de {{ formatMoney(campaign.goal_amount) }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="h4 mb-0">{{ Math.round(progress) }}%</div>
                                            <div class="text-muted small">atingido</div>
                                        </div>
                                    </div>

                                    <div class="row g-2 mt-2 text-center">
                                        <div class="col-6">
                                            <div class="fw-semibold">{{ campaign.supporters_count ?? 0 }}</div>
                                            <div class="text-muted small">apoiadores</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="fw-semibold">{{ daysRemaining }}</div>
                                            <div class="text-muted small">dias</div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-3" />

                                <template v-if="user">
                                    <div v-if="!isCampaignOpen" class="alert alert-secondary mb-3" role="alert">
                                        Esta campanha não está aceitando apoios no momento.
                                    </div>

                                    <form @submit.prevent="submit">
                                        <div class="mb-2">
                                            <label class="form-label">Valor do apoio</label>
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input
                                                    v-model="amount"
                                                    type="number"
                                                    class="form-control"
                                                    min="1"
                                                    step="0.01"
                                                    :disabled="submitting || !isCampaignOpen"
                                                    required
                                                />
                                            </div>
                                            <div v-if="selectedReward" class="form-text">
                                                Mínimo para esta recompensa: <strong>{{ formatMoney(selectedReward.min_amount) }}</strong>
                                            </div>
                                        </div>

                                        <div v-if="(campaign.rewards || []).length" class="mb-3">
                                            <label class="form-label">Recompensa</label>
                                            <select v-model="rewardId" class="form-select" :disabled="submitting || !isCampaignOpen">
                                                <option :value="null">Sem recompensa</option>
                                                <option
                                                    v-for="r in sortedRewards"
                                                    :key="r.id"
                                                    :value="r.id"
                                                    :disabled="!isRewardAvailable(r)"
                                                >
                                                    {{ r.title }} — {{ formatMoney(r.min_amount) }}
                                                    <template v-if="!isRewardAvailable(r)"> (Esgotada)</template>
                                                </option>
                                            </select>
                                        </div>

                                        <button
                                            type="submit"
                                            class="btn btn-success w-100"
                                            :disabled="submitting || !isCampaignOpen"
                                        >
                                            <i class="bi bi-heart-fill"></i>
                                            {{ submitting ? 'Processando…' : 'Apoiar agora' }}
                                        </button>

                                        <div v-if="message" class="text-muted small mt-2">{{ message }}</div>
                                    </form>
                                </template>

                                <template v-else>
                                    <p class="text-muted mb-3">Faça login para apoiar este projeto.</p>
                                    <RouterLink to="/login" class="btn btn-primary w-100">Entrar</RouterLink>
                                    <RouterLink to="/register" class="btn btn-outline-primary w-100 mt-2">Cadastrar</RouterLink>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="container py-4 text-muted">Campanha não encontrada.</div>
    </div>
</template>

<script setup>
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { apiGet, apiPost } from '../api';
import { applyCampaignSeo } from '../seo';
import DOMPurify from 'dompurify';
import { marked } from 'marked';

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
const supportBox = ref(null);

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

const isCampaignOpen = computed(() => {
    const c = campaign.value;
    if (!c) return false;
    if (String(c.status || '').toLowerCase() !== 'active') return false;
    return Number(daysRemaining.value || 0) > 0;
});

const sortedRewards = computed(() => {
    const rewards = campaign.value?.rewards || [];
    return [...rewards].sort((a, b) => Number(a.min_amount || 0) - Number(b.min_amount || 0));
});

const selectedReward = computed(() => {
    if (!rewardId.value) return null;
    return (campaign.value?.rewards || []).find((r) => String(r.id) === String(rewardId.value)) || null;
});

const storyHtml = computed(() => {
    const raw = String(campaign.value?.description || '').trim();
    if (!raw) return '';
    try {
        const html = marked.parse(raw, {
            gfm: true,
            breaks: true,
            headerIds: false,
            mangle: false,
        });
        return DOMPurify.sanitize(String(html || ''), {
            USE_PROFILES: { html: true },
        });
    } catch {
        return '';
    }
});

function centsToAmountInput(cents) {
    return (Number(cents || 0) / 100).toFixed(2);
}

async function scrollToSupport() {
    await nextTick();
    const el = supportBox.value;
    if (!el) return;
    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

async function selectReward(r) {
    rewardId.value = r?.id ?? null;
    const min = Number(r?.min_amount || 0);
    amount.value = centsToAmountInput(min);
    await scrollToSupport();
}

async function selectNoReward() {
    rewardId.value = null;
    await scrollToSupport();
}

async function fetchCampaign() {
    loading.value = true;
    campaign.value = await apiGet(`/api/campaigns/${props.slug}`);
    progress.value = calcProgress(campaign.value);
    daysRemaining.value = calcDaysRemaining(campaign.value);

    applyCampaignSeo(campaign.value);

    // Escolha inicial de valor: se houver recompensa selecionada, usa mínimo; senão, um valor sugerido.
    if (selectedReward.value) {
        amount.value = centsToAmountInput(selectedReward.value.min_amount);
    } else if (!amount.value) {
        amount.value = '25.00';
    }
    loading.value = false;
}

async function submit() {
    message.value = '';
    submitting.value = true;

    try {
        if (selectedReward.value) {
            const min = Number(selectedReward.value.min_amount || 0) / 100;
            if (Number(amount.value) < min) {
                message.value = `O valor mínimo para esta recompensa é ${formatMoney(selectedReward.value.min_amount)}.`;
                submitting.value = false;
                return;
            }
        }

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

<style scoped>
.campaign-story :deep(p) {
    margin-bottom: 0.75rem;
}

.campaign-story :deep(h1),
.campaign-story :deep(h2),
.campaign-story :deep(h3),
.campaign-story :deep(h4) {
    margin-top: 1rem;
    margin-bottom: 0.5rem;
}

.campaign-story :deep(ul),
.campaign-story :deep(ol) {
    margin-bottom: 0.75rem;
}

.campaign-story :deep(a) {
    text-decoration: none;
}

.campaign-story :deep(a:hover) {
    text-decoration: underline;
}
</style>
